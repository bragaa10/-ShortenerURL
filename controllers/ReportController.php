<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\ShortUrl;
use app\models\ScanLog;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends Controller
{
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
     * Shows the report generation form.
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $isAdmin = Yii::$app->user->identity->isAdmin();

        $query = ShortUrl::find();
        if (!$isAdmin) {
            $query->where(['user_id' => $userId]);
        }
        $links = $query->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', [
            'links' => $links,
        ]);
    }

    /**
     * Generates and streams the PDF report.
     */
    public function actionExport()
    {
        $request = Yii::$app->request;
        $linkIds = $request->post('link_ids');
        $allLinks = $request->post('all_links');
        
        $userId = Yii::$app->user->id;
        $isAdmin = Yii::$app->user->identity->isAdmin();

        $query = ShortUrl::find();
        if (!$isAdmin) {
            $query->andWhere(['user_id' => $userId]);
        }

        if (!$allLinks && !empty($linkIds)) {
            $query->andWhere(['id' => $linkIds]);
        }

        $links = $query->all();
        if (empty($links)) {
            Yii::$app->session->setFlash('error', 'No links selected for the report.');
            return $this->redirect(['index']);
        }

        // Gather Data for the last 30 days
        $data = $this->gatherReportData($links);

        // Generate PDF
        return $this->generatePdf($data);
    }

    protected function gatherReportData($links)
    {
        $linkIds = array_column($links, 'id');
        $days = 30;
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        // 1. Daily Scans (Chart data)
        $dailyScans = ScanLog::find()
            ->select(["DATE(FROM_UNIXTIME(accessed_at)) as scan_date", "COUNT(*) as count"])
            ->where(['short_url_id' => $linkIds])
            ->andWhere(['>=', 'FROM_UNIXTIME(accessed_at)', $startDate])
            ->groupBy('scan_date')
            ->orderBy('scan_date')
            ->asArray()
            ->all();

        $chartLabels = [];
        $chartValues = [];
        $scanMap = array_column($dailyScans, 'count', 'scan_date');

        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d/m', strtotime($date));
            $chartValues[] = (int)($scanMap[$date] ?? 0);
        }

        // 2. Summary
        $totalScans = ScanLog::find()->where(['short_url_id' => $linkIds])->count();
        $uniqueScans = ScanLog::find()->where(['short_url_id' => $linkIds])->groupBy('ip_address')->count();

        // 3. Top Countries
        $countries = ScanLog::find()
            ->select(['country', 'count' => 'COUNT(*)'])
            ->where(['short_url_id' => $linkIds])
            ->andWhere(['IS NOT', 'country', null])
            ->groupBy('country')
            ->orderBy(['count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // 4. Device Distribution
        $devices = ScanLog::find()
            ->select(['device_type', 'count' => 'COUNT(*)'])
            ->where(['short_url_id' => $linkIds])
            ->andWhere(['IS NOT', 'device_type', null])
            ->groupBy('device_type')
            ->orderBy(['count' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'links' => $links,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'totalScans' => $totalScans,
            'uniqueScans' => $uniqueScans,
            'countries' => $countries,
            'devices' => $devices,
            'reportDate' => date('d/m/Y H:i'),
            'period' => "Last $days days"
        ];
    }

    protected function generatePdf($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Critical for QuickChart images
        
        $dompdf = new Dompdf($options);
        
        // Render the view as HTML string
        $html = $this->renderPartial('pdf_template', $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'report_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
