<?php

namespace app\controllers;

use app\core\InitController;
use app\lib\UserOperation;
use app\lib\UserOperations;
use app\models\NewsModels;

class NewsController extends InitController
{
    public function behaviors()
    {
        return [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['list'],
                        'roles' => [UserOperation::RoleUser, UserOperation::RoleAdmin],
                        'matchCallBack' => function () {
                            $this->redirect('/user/login');
                        }
                    ],
                    [
                        'actions' => ['add', 'edit', 'delete'],
                        'roles' => [UserOperation::RoleAdmin],
                        'matchCallback' => function () {
                            $this->redirect('/news/list');
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionList()
    {
        $this->view->title = 'Новости';
        $news_model=new NewsModels();
        $news=$news_model->getListNews();
        $this->render('list', [
            'sidebar' => UserOperation::getMenuLinks(),
            'news'=>$news,
            'role'=> UserOperation::getRoleUser(),
        ]);
    }

    public function actionAdd()
    {
        $this->view->title = 'Добавление новости';
        $error_message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['btn_news_add_form'])) {
            $news_data = (!empty($_POST['news']) ? $_POST['news'] : null);
            if (!empty($news_data)) {
                $userModel = new NewsModels();
                $result_add = $userModel->add($news_data);
                if ($result_add['result']) {
                    $this->redirect('/news/list');
                } else {
                    $error_message = $result_add['error_message'];
                }
            }
        }

        $this->render('add', [
            'sidebar' => UserOperation::getMenuLinks(),
            'error_message' => $error_message
        ]);
    }
    public  function actionEdit(){
        $this->view->title='Редактирование новости';
        $news_id= !empty($_GET['news_id']) ? $_GET['news_id'] : null;
        $news=null;
        $error_message = '';


        if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['btn_news_edit_form'])){
            $news_data = !empty($_POST['news']) ? $_POST['news'] : null;

            if (!empty($news_data)){
                $newsModel= new NewsModels();
                $result_edit= $newsModel->edit($news_id, $news_data);
                if ($result_edit['result']){
                    $this->redirect('/news/list');
                } else{
                    $error_message = $result_edit['error_message'];
                }
            }
        }
        if (!empty($news_id)){
            $news_model= new NewsModels();
            $news = $news_model->getNewsById($news_id);
            if (empty($news)){
                $error_message='Новость не найдет';
            }
        } else{
            $error_message='Отсутствует идентификатор записи';
        }
        $this->render('edit', [
            'sidebar'=> UserOperation::getMenuLinks(),
            'news'=> $news,
            'error_message'=>$error_message
        ]);
    }
    public function actionDelete(){
        $this->view->title='Редактирование новостей';
        $news_id=!empty($_GET['news_id'])? $_GET['news_id'] : null;
        $news=null;
        $error_message='';

        if (!empty($news_id)){
            $news_model= new NewsModels();
            $news = $news_model->getNewsById($news_id);
            if (!empty($news)){
                $result_delete=$news_model->deleteById($news_id);
                if ($result_delete['result']){
                    $this->redirect('/news/list');
                }else{
                    $error_message = $result_delete['error_message'];
                }
            } else{
                $error_message = 'Новость не найдена';
            }
        } else{
            $error_message='Отсутствие индентификатора записи';
        }
        $this->render('delete',[
            'sidebar'=> UserOperation::getMenuLinks(),
            'news'=>$news,
            'error_massage'=>$error_message
        ]);
    }

}
