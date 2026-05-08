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
    public function actionIndex()
    {
        $searchModel = new ScanLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // Non-admin: only see own scans
        if (!Yii::$app->user->identity->isAdmin()) {
            $dataProvider->query->innerJoin('short_url', 'short_url.id = scan_log.short_url_id')
                ->andWhere(['short_url.user_id' => Yii::$app->user->id]);
        }

        $dataProvider->sort = ['defaultOrder' => ['id' => SORT_DESC]];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        Yii::$app->session->setFlash('success', 'Registo eliminado.');
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

        $rows = $query->asArray()->all();

        // Build CSV content
        $output = fopen('php://temp', 'r+');

        // Header row
        fputcsv($output, [
            'ID', 'Link ID', 'Data/Hora', 'IP', 'País', 'Cidade',
            'Dispositivo', 'OS', 'Browser', 'Idioma', 'Fonte',
            'Referer', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Term', 'UTM Content',
        ]);

        // Data rows
        foreach ($rows as $row) {
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
        throw new NotFoundHttpException('O registo solicitado não existe.');
    }
}
