<?php

namespace app\controllers;

use app\models\Article;
use yii\data\Pagination;
use yii\web\Controller;

class SiteController extends Controller
{


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],

        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Article::find()->orderBy(["modified_at" => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 10]);
        $posts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('index',
            [
                "articles" => $posts,
                "pages" => $pages
            ]
        );
    }


}
