<?php

namespace app\controllers;

use Yii;
use app\models\ShortUrl;
use app\models\ScanLog;
use app\models\Campaign;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * DashboardController displays the admin dashboard with statistics.
 */
class DashboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays the dashboard with aggregated statistics.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $isAdmin = Yii::$app->user->identity->isAdmin();

        // Base queries scoped to user (admin sees all)
        $linkQuery = ShortUrl::find();
        $scanQuery = ScanLog::find()->innerJoin('short_url', 'short_url.id = scan_log.short_url_id');
        $campaignQuery = Campaign::find();

        if (!$isAdmin) {
            $linkQuery->andWhere(['user_id' => $userId]);
            $scanQuery->andWhere(['short_url.user_id' => $userId]);
            $campaignQuery->andWhere(['user_id' => $userId]);
        }

        // Total counts
        $totalLinks = (int) (clone $linkQuery)->count();
        $totalScans = (int) (clone $scanQuery)->count();
        $totalCampaigns = (int) (clone $campaignQuery)->count();
        $totalQrCodes = (int) (clone $linkQuery)->andWhere(['IS NOT', 'qr_code_path', null])->count();

        // Scans today
        $todayStart = strtotime('today');
        $scansToday = (int) (clone $scanQuery)->andWhere(['>=', 'scan_log.accessed_at', $todayStart])->count();

        // Unique scans (distinct IPs)
        $uniqueScans = (int) (clone $scanQuery)->select('scan_log.ip_address')->distinct()->count('ip_address');

        // Daily scans for last 30 days (for chart)
        $thirtyDaysAgo = strtotime('-30 days');
        $dailyScansQuery = ScanLog::find()
            ->select([
                'scan_date' => 'DATE(FROM_UNIXTIME(scan_log.accessed_at))',
                'scan_count' => 'COUNT(*)',
            ])
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->andWhere(['>=', 'scan_log.accessed_at', $thirtyDaysAgo]);

        if (!$isAdmin) {
            $dailyScansQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $dailyScans = $dailyScansQuery
            ->groupBy('scan_date')
            ->orderBy('scan_date')
            ->asArray()
            ->all();

        // Fill in missing days
        $chartLabels = [];
        $chartData = [];
        $dailyScanMap = [];
        foreach ($dailyScans as $row) {
            $dailyScanMap[$row['scan_date']] = (int) $row['scan_count'];
        }
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d/m', strtotime($date));
            $chartData[] = $dailyScanMap[$date] ?? 0;
        }

        // Top 5 most accessed links
        $topLinksQuery = ShortUrl::find()
            ->select([
                'short_url.*',
                'scan_count' => 'COUNT(scan_log.id)',
            ])
            ->leftJoin('scan_log', 'scan_log.short_url_id = short_url.id')
            ->groupBy('short_url.id')
            ->orderBy(['scan_count' => SORT_DESC])
            ->limit(5);

        if (!$isAdmin) {
            $topLinksQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $topLinks = $topLinksQuery->asArray()->all();

        // Device distribution
        $devicesQuery = ScanLog::find()
            ->select(['device_type', 'device_count' => 'COUNT(*)'])
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->andWhere(['IS NOT', 'device_type', null])
            ->groupBy('device_type')
            ->orderBy(['device_count' => SORT_DESC]);

        if (!$isAdmin) {
            $devicesQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $devices = $devicesQuery->asArray()->all();

        // Browser distribution
        $browsersQuery = ScanLog::find()
            ->select(['browser', 'browser_count' => 'COUNT(*)'])
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->andWhere(['IS NOT', 'browser', null])
            ->groupBy('browser')
            ->orderBy(['browser_count' => SORT_DESC])
            ->limit(6);

        if (!$isAdmin) {
            $browsersQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $browsers = $browsersQuery->asArray()->all();

        // Country distribution (Top list)
        $countriesQuery = ScanLog::find()
            ->select(['country', 'country_count' => 'COUNT(*)'])
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->andWhere(['IS NOT', 'country', null])
            ->groupBy('country')
            ->orderBy(['country_count' => SORT_DESC])
            ->limit(10);

        if (!$isAdmin) {
            $countriesQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $countries = $countriesQuery->asArray()->all();

        // Heatmap data (All countries with codes)
        $heatmapQuery = ScanLog::find()
            ->select(['country_code', 'count' => 'COUNT(*)'])
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->andWhere(['IS NOT', 'country_code', null])
            ->groupBy('country_code');

        if (!$isAdmin) {
            $heatmapQuery->andWhere(['short_url.user_id' => $userId]);
        }

        $heatmapData = [];
        foreach ($heatmapQuery->asArray()->all() as $row) {
            $heatmapData[$row['country_code']] = (int) $row['count'];
        }

        // Top campaigns
        $topCampaignsQuery = Campaign::find()
            ->select([
                'campaign.*',
                'link_count' => 'COUNT(DISTINCT short_url.id)',
                'scan_count' => 'COUNT(scan_log.id)',
            ])
            ->leftJoin('short_url', 'short_url.campaign_id = campaign.id')
            ->leftJoin('scan_log', 'scan_log.short_url_id = short_url.id')
            ->groupBy('campaign.id')
            ->orderBy(['scan_count' => SORT_DESC])
            ->limit(5);

        if (!$isAdmin) {
            $topCampaignsQuery->andWhere(['campaign.user_id' => $userId]);
        }

        $topCampaigns = $topCampaignsQuery->asArray()->all();

        return $this->render('index', [
            'totalLinks' => $totalLinks,
            'totalScans' => $totalScans,
            'totalCampaigns' => $totalCampaigns,
            'totalQrCodes' => $totalQrCodes,
            'scansToday' => $scansToday,
            'uniqueScans' => $uniqueScans,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'topLinks' => $topLinks,
            'devices' => $devices,
            'browsers' => $browsers,
            'countries' => $countries,
            'heatmapData' => $heatmapData,
            'topCampaigns' => $topCampaigns,
        ]);
    }
}
