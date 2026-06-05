<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;

class ChatController extends Controller
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
     * Handles the chatbot query using Gemini API.
     */
    public function actionQuery()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $message = Yii::$app->request->post('message');

        if (empty($message)) {
            return ['response' => "Como posso ajudar você hoje?"];
        }

        // Context for the AI to know about the site
        $context = "Você é o 'Linky', assistente do Encurtador de URLs. 
        Instruções de uso para o usuário:
        - Para criar links: Vá em 'Links' no menu lateral e clique em 'New Link'.
        - Para QR Codes: Após criar um link, vá em 'Links', clique em 'View' e selecione 'Download QR'.
        - Para Relatórios: Vá em 'Reports' no menu lateral.
        - Para Campanhas: Vá em 'Campaigns' para organizar seus links.
        Mantenha as respostas curtíssimas, diretas e sem enrolação. Responda no mesmo idioma que o usuário usar. Use apenas texto puro, sem asteriscos ou negrito.";

        $result = Yii::$app->gemini->query($message, $context);

        return ['response' => $result['response']];
    }
}
