<?php

namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use common\widgets\ueditor\UeditorAction;
use yii\web\Controller;

class ArticleController extends Controller{
    //>>文章列表
    public function actionIndex(){
        $rows = Article::find()->where(['>=','status','0'])->all();
        $categorys = ArticleCategory::find()->all();
        $val = [];
        foreach($categorys as $category){
            $val[$category->id]=$category->name;
        }
        return $this->render('index',['rows'=>$rows,'val'=>$val]);
    }
    //>>文章信息添加
    public function actionAdd(){
        $model = new Article();
        $detail = new ArticleDetail();
        $request = \Yii::$app->request;
        $categorys = ArticleCategory::find()->all();
        $id=[];
        foreach ($categorys as $category){
            $id[$category->id] = $category->name;
        }
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->create_time = time();
                $model->save();
                //>>文章内容提交
                $detail->load($request->post());
                if($detail->validate()){
                    $detail->article_id = $model->id;
                    $detail->save();
                    \Yii::$app->session->setFlash('success','添加文章信息成功');
                    return $this->redirect(['article/index']);
                }
            }
        }else{
            return $this->render('update',['model'=>$model,'detail'=>$detail,'id'=>$id]);
        }
    }
    //>>文章信息修改
    public function actionEdit($id){
        $model = Article::find()->where(['id'=>$id])->one();
        $detail =ArticleDetail::find()->where(['article_id'=>$id])->one();
       /* var_dump($model);
        die;*/
        $request = \Yii::$app->request;
        $categorys = ArticleCategory::find()->all();
        $id=[];
        foreach ($categorys as $category){
            $id[$category->id] = $category->name;
        }
        if($request->isPost){
            $model->load($request->post());
            $detail->load($request->post());
            if($model->validate()){
                $model->save();
                $detail->save();
                \Yii::$app->session->setFlash('success','修改文章信息成功');
                return $this->redirect(['article/index']);
            }
        }else{
            return $this->render('update',['model'=>$model,'detail'=>$detail,'id'=>$id]);
        }
    }
    //>>删除文章信息
    public function actionDelete($id){
        $row = Article::find()->where(['id'=>$id])->one();
        $row->status = -1;
        $res = $row->save();
        return json_encode($res);
    }
    //>>富文本编辑器
    public function actions()
    {
        return [
            //>>富文本编辑器
            'Ueditor'=>[
                'class'=>UeditorAction::className(),
                'config'=>[
                    //>>上传图片配置
                    'imageUrlPrefix'=>'',
                    'imagePathFormat'=>'/image/{yyyy}{mm}{dd}/{time}{rand:6}'
                ]
            ]
        ];
    }
}