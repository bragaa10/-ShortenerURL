<?php

namespace app\controllers;

use Yii;
use app\models\ScanLog;
use app\models\ScanLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ScanlogController implements the CRUD actions for ScanLog model.
 */
class ScanlogController extends Controller
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
     * Lists all ScanLog models.
     *
     * @return string
     */
    public function actionIndex($short_url_id = null)
    {
        $searchModel = new ScanLogSearch();
        
        if ($short_url_id) {
            $shortUrl = \app\models\ShortUrl::findOne($short_url_id);
            
            // Security: check ownership
            if (!$shortUrl || (!Yii::$app->user->identity->isAdmin() && $shortUrl->user_id !== Yii::$app->user->id)) {
                Yii::$app->session->setFlash('error', 'The requested link was not found or you do not have permission to access it.');
                return $this->redirect(['scanlog/index']);
            }
            
            // View logs for a specific link
            $searchModel->short_url_id = $short_url_id;
            $dataProvider = $searchModel->search($this->request->queryParams);
            
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'shortUrl' => $shortUrl,
            ]);
        }

        // Otherwise, show list of links to choose from
        $linkSearchModel = new \app\models\ShortUrlSearch();
        $linkDataProvider = $linkSearchModel->search($this->request->queryParams);
        
        // Non-admin: only see own links
        if (!Yii::$app->user->identity->isAdmin()) {
            $linkDataProvider->query->andWhere(['short_url.user_id' => Yii::$app->user->id]);
        }

        return $this->render('links', [
            'searchModel' => $linkSearchModel,
            'dataProvider' => $linkDataProvider,
        ]);
    }

    /**
     * Displays a single ScanLog model.
     *
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Security: check if user owns the link this log belongs to
        if ($model->shortUrl && !Yii::$app->user->identity->isAdmin() && $model->shortUrl->user_id !== Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You do not have permission to view this log entry.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ScanLog model.
     *
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Record deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Exports scan logs to CSV file.
     *
     * @return Response
     */
    public function actionExportCsv()
    {
        $query = ScanLog::find()->orderBy(['accessed_at' => SORT_DESC]);

        // Non-admin: only export own scans
        if (!Yii::$app->user->identity->isAdmin()) {
            $query->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
                ->andWhere(['short_url.user_id' => Yii::$app->user->id]);
        }

        // Build CSV content using batch() to avoid memory exhaustion
        $output = fopen('php://temp', 'r+');

        // Header row
        fputcsv($output, [
            'ID', 'Link ID', 'Date/Time', 'IP', 'Country', 'City',
            'Device', 'OS', 'Browser', 'Language', 'Source',
            'Referer', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Term', 'UTM Content',
        ]);

        // Data rows — batch of 200 at a time to avoid memory exhaustion
        foreach ($query->asArray()->batch(200) as $batch) {
            foreach ($batch as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['short_url_id'],
                    date('Y-m-d H:i:s', $row['accessed_at']),
                    $row['ip_address'] ?? '',
                    $row['country'] ?? '',
                    $row['city'] ?? '',
                    $row['device_type'] ?? '',
                    $row['os'] ?? '',
                    $row['browser'] ?? '',
                    $row['language'] ?? '',
                    $row['source'] ?? '',
                    $row['referer'] ?? '',
                    $row['utm_source'] ?? '',
                    $row['utm_medium'] ?? '',
                    $row['utm_campaign'] ?? '',
                    $row['utm_term'] ?? '',
                    $row['utm_content'] ?? '',
                ]);
            }
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $filename = 'scan-logs-' . date('Y-m-d') . '.csv';

        return Yii::$app->response->sendContentAsFile(
            $csvContent,
            $filename,
            ['mimeType' => 'text/csv', 'inline' => false]
        );
    }

    /**
     * Finds the ScanLog model.
     *
     * @param int $id ID
     * @return ScanLog
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ScanLog::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested log entry does not exist.');
    }
}
