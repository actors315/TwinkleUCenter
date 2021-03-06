<?php
/**
 * Created by PhpStorm.
 * User: xiehuanjin
 * Date: 2017/11/16
 * Time: 10:34
 */

namespace app\controllers;

use app\base\api\Controller;
use app\helpers\Str;
use twinkle\service\Api;
use Yii;

class ApiController extends Controller
{

    public function actionIndex()
    {
        $serviceName = Yii::$app->request->get('name', 'NotFound');
        $class = '\\app\\services\\' . Str::ucWords($serviceName) . 'Service';

        if (Yii::$app->request->isPost) {
            $methodName = Yii::$app->request->post('method', 'error');
            $params = Yii::$app->request->post();
            unset($params['method']);
        } else {
            $methodName = Yii::$app->request->get('method', 'error');
            $params = Yii::$app->request->get();
            unset($params['method']);
        }
        $methodName = Str::ucWords($methodName, '-', true);

        list($instance, $method, $args) = (new Api([
            'type' => 'http',
            'driver' => 'Http',
            'object' => $class,
        ]))->parseRequest($methodName, $params);

        return $this->asJson(($method->invokeArgs($instance, $args)));
    }

}