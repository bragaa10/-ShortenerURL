<?php

namespace app\controllers;

use Yii;
use app\models\ShortUrl;
use app\models\ShortUrlSearch;
use app\models\ScanLog;
use app\models\Campaign;
use app\components\QrCodeGenerator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ShortUrlController implements the CRUD actions for ShortUrl model.
 */
class ShortUrlController extends Controller
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
     * Lists all ShortUrl models for the current user.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new \app\models\ShortUrlSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        // Ensure users only see their own links if not admin
        if (!Yii::$app->user->identity->isAdmin()) {
            $dataProvider->query->andWhere(['short_url.user_id' => Yii::$app->user->id]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ShortUrl model.
     *
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new ShortUrl model with auto short code and QR generation.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new ShortUrl();

        // Get campaigns for dropdown
        $campaigns = Campaign::find()
            ->where(['user_id' => Yii::$app->user->id, 'status' => Campaign::STATUS_ACTIVE])
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Convert date to timestamp
                if (!empty($model->expires_at)) {
                    $model->expires_at = strtotime($model->expires_at . ' 23:59:59');
                }
                
                if ($model->save()) {
                    // Generate QR code with source tracking
                    $qrUrl = $model->getShortUrl();
                    $qrUrl .= (strpos($qrUrl, '?') === false ? '?' : '&') . 'source=qr';
                    
                    $generator = new QrCodeGenerator();
                    $model->qr_code_path = $generator->generate($qrUrl, $model->id);
                    $model->save(false, ['qr_code_path']);

                    Yii::$app->session->setFlash('success', 'Short link created successfully!');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Updates an existing ShortUrl model.
     *
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $campaigns = Campaign::find()
            ->where(['user_id' => Yii::$app->user->id, 'status' => Campaign::STATUS_ACTIVE])
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Convert date to timestamp
            if (!empty($model->expires_at)) {
                $model->expires_at = strtotime($model->expires_at . ' 23:59:59');
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Link updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Format timestamp for date input
        if ($model->expires_at && is_numeric($model->expires_at)) {
            $model->expires_at = date('Y-m-d', $model->expires_at);
        }

        return $this->render('update', [
            'model' => $model,
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Displays statistics for a single ShortUrl.
     *
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStats($id)
    {
        $model = $this->findModel($id);

        // Daily scans for chart
        $dailyScans = $model->getDailyScans(30);
        $chartLabels = [];
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d/m', strtotime($date));
            $chartData[] = $dailyScans[$date] ?? 0;
        }

        // Top countries
        $countries = ScanLog::find()
            ->select(['country', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->andWhere(['IS NOT', 'country', null])
            ->groupBy('country')
            ->orderBy(['cnt' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Device distribution
        $devices = ScanLog::find()
            ->select(['device_type', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->andWhere(['IS NOT', 'device_type', null])
            ->groupBy('device_type')
            ->orderBy(['cnt' => SORT_DESC])
            ->asArray()
            ->all();

        // Browser distribution
        $browsers = ScanLog::find()
            ->select(['browser', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->andWhere(['IS NOT', 'browser', null])
            ->groupBy('browser')
            ->orderBy(['cnt' => SORT_DESC])
            ->limit(6)
            ->asArray()
            ->all();

        // OS distribution
        $operatingSystems = ScanLog::find()
            ->select(['os', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->andWhere(['IS NOT', 'os', null])
            ->groupBy('os')
            ->orderBy(['cnt' => SORT_DESC])
            ->asArray()
            ->all();

        // Referers
        $referers = ScanLog::find()
            ->select(['referer', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->andWhere(['IS NOT', 'referer', null])
            ->andWhere(['!=', 'referer', ''])
            ->groupBy('referer')
            ->orderBy(['cnt' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Source distribution (QR vs Click vs UTM)
        $sources = ScanLog::find()
            ->select(['source', 'cnt' => 'COUNT(*)'])
            ->where(['short_url_id' => $model->id])
            ->groupBy('source')
            ->orderBy(['cnt' => SORT_DESC])
            ->asArray()
            ->all();

        // Recent scans
        $recentScans = ScanLog::find()
            ->where(['short_url_id' => $model->id])
            ->orderBy(['accessed_at' => SORT_DESC])
            ->limit(20)
            ->all();

        return $this->render('stats', [
            'model' => $model,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'countries' => $countries,
            'devices' => $devices,
            'browsers' => $browsers,
            'operatingSystems' => $operatingSystems,
            'referers' => $referers,
            'sources' => $sources,
            'recentScans' => $recentScans,
        ]);
    }

    /**
     * Re-generates QR code marker for a ShortUrl.
     * The actual QR code is rendered client-side via JavaScript.
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionGenerateQr($id)
    {
        $model = $this->findModel($id);

        // Delete old QR if it exists
        if (!empty($model->qr_code_path) && strpos($model->qr_code_path, 'js:') !== 0) {
            $generator = new QrCodeGenerator();
            $generator->delete($model->qr_code_path);
        }

        // Generate new QR code with source tracking
        $qrUrl = $model->getShortUrl();
        $qrUrl .= (strpos($qrUrl, '?') === false ? '?' : '&') . 'source=qr';

        $generator = new QrCodeGenerator();
        $model->qr_code_path = $generator->generate($qrUrl, $model->id);
        $model->save(false, ['qr_code_path']);

        Yii::$app->session->setFlash('success', 'QR Code generated successfully!');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Downloads the QR code as PNG.
     * Since QR is generated client-side, this action redirects to the view
     * with a flag so JavaScript triggers the download automatically.
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDownloadQr($id)
    {
        $model = $this->findModel($id);
        
        if (empty($model->qr_code_path) || strpos($model->qr_code_path, 'js:') === 0) {
            // Fallback: generate it now
            $this->actionGenerateQr($id);
            $model->refresh();
        }

        $filePath = Yii::getAlias('@webroot/' . $model->qr_code_path);
        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, "qrcode-{$model->short_code}.png");
        }

        Yii::$app->session->setFlash('error', 'QR Code file not found.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Downloads the QR code as SVG (high-res PNG fallback).
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDownloadQrSvg($id)
    {
        $model = $this->findModel($id);
        
        // Generate SVG on the fly using Endroid
        if (class_exists('\Endroid\QrCode\QrCode')) {
            $qrUrl = $model->getShortUrl();
            $qrUrl .= (strpos($qrUrl, '?') === false ? '?' : '&') . 'source=qr';

            $qrCode = new \Endroid\QrCode\QrCode(
                data: $qrUrl,
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Low,
                size: 600,
                margin: 10,
                roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
            );

            $writer = new \Endroid\QrCode\Writer\SvgWriter();
            $result = $writer->write($qrCode);
            
            return Yii::$app->response->sendContentAsFile(
                $result->getString(),
                "qrcode-{$model->short_code}.svg",
                ['mimeType' => 'image/svg+xml']
            );
        }

        Yii::$app->session->setFlash('error', 'SVG generation not supported in this environment.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing ShortUrl model.
     *
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Delete QR code file if it's a local file (not a JS marker)
        if (!empty($model->qr_code_path) && strpos($model->qr_code_path, 'js:') !== 0) {
            $qrGenerator = new QrCodeGenerator();
            $qrGenerator->delete($model->qr_code_path);
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Link deleted successfully.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the ShortUrl model with ownership check.
     *
     * @param int $id ID
     * @return ShortUrl the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = ShortUrl::findOne(['id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException('O link solicitado não existe.');
        }

        // Ownership check: non-admins can only access their own links
        if (!Yii::$app->user->identity->isAdmin() && $model->user_id != Yii::$app->user->id) {
            throw new NotFoundHttpException('O link solicitado não existe.');
        }

        return $model;
    }
}
