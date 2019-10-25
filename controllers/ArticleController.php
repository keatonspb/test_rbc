<?php


namespace app\controllers;


use app\models\Article;
use yii\web\Controller;

class ArticleController extends Controller
{
    public function actionIndex($id) {
        $Article = Article::findOne($id);
        if(!$Article) {
            throw new \HttpException("Article not found", 404);
        }
        return $this->render('index',
            [
                "article" => $Article
            ]

        );
    }
}
