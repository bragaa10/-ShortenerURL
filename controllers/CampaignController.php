<?php

namespace app\controllers;

use Yii;
use app\models\Campaign;
use app\models\CampaignSearch;
use app\models\ScanLog;
use app\models\ShortUrl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CampaignController implements the CRUD actions for Campaign model.
 */
class CampaignController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Campaign models for the current user.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CampaignSearch();
        $userId = Yii::$app->user->identity->isAdmin() ? null : Yii::$app->user->id;
        $dataProvider = $searchModel->search($this->request->queryParams, $userId);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Campaign model.
     *
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Campaign model.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Campaign();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Campanha criada com sucesso.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Campaign model.
     *
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Campanha atualizada com sucesso.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Campaign model.
     *
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Campanha eliminada com sucesso.');

        return $this->redirect(['index']);
    }

    /**
     * Displays aggregated statistics for a campaign.
     *
     * @param int $id
     * @return string
     */
    public function actionStats($id)
    {
        $model = $this->findModel($id);

        // Aggregate statistics for all links in this campaign
        $scanQuery = ScanLog::find()
            ->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
            ->where(['short_url.campaign_id' => $id]);

        $totalScans = (int) (clone $scanQuery)->count();
        $uniqueScans = (int) (clone $scanQuery)->select('scan_log.ip_address')->distinct()->count('ip_address');

        // Scans by day (last 30 days)
        $thirtyDaysAgo = strtotime('-30 days');
        $dailyScans = (clone $scanQuery)
            ->select([
                'scan_date' => 'DATE(FROM_UNIXTIME(scan_log.accessed_at))',
                'scan_count' => 'COUNT(*)',
            ])
            ->andWhere(['>=', 'scan_log.accessed_at', $thirtyDaysAgo])
            ->groupBy('scan_date')
            ->orderBy('scan_date')
            ->asArray()
            ->all();

        // Prepare chart data
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

        // Top links in this campaign
        $topLinks = ShortUrl::find()
            ->select(['short_url.*', 'scan_count' => 'COUNT(scan_log.id)'])
            ->leftJoin('scan_log', 'scan_log.short_url_id = short_url.id')
            ->where(['short_url.campaign_id' => $id])
            ->groupBy('short_url.id')
            ->orderBy(['scan_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // Device distribution
        $devices = (clone $scanQuery)
            ->select(['device_type', 'device_count' => 'COUNT(*)'])
            ->andWhere(['IS NOT', 'device_type', null])
            ->groupBy('device_type')
            ->orderBy(['device_count' => SORT_DESC])
            ->asArray()
            ->all();

        // Browser distribution
        $browsers = (clone $scanQuery)
            ->select(['browser', 'browser_count' => 'COUNT(*)'])
            ->andWhere(['IS NOT', 'browser', null])
            ->groupBy('browser')
            ->orderBy(['browser_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // Country distribution
        $countries = (clone $scanQuery)
            ->select(['country', 'country_count' => 'COUNT(*)'])
            ->andWhere(['IS NOT', 'country', null])
            ->groupBy('country')
            ->orderBy(['country_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        return $this->render('stats', [
            'model' => $model,
            'totalScans' => $totalScans,
            'uniqueScans' => $uniqueScans,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'topLinks' => $topLinks,
            'devices' => $devices,
            'browsers' => $browsers,
            'countries' => $countries,
        ]);
    }

    /**
     * Finds the Campaign model based on its primary key value.
     * Enforces ownership check for non-admin users.
     *
     * @param int $id ID
     * @return Campaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Campaign::findOne(['id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException('A campanha solicitada não existe.');
        }

        // Ownership check
        if (!Yii::$app->user->identity->isAdmin() && $model->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('A campanha solicitada não existe.');
        }

        return $model;
    }
}
