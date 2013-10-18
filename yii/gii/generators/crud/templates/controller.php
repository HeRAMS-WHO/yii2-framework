<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\base\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);

$pks = $generator->getTableSchema()->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?=StringHelper::dirname(ltrim($generator->controllerClass, '\\')); ?>;

use <?=ltrim($generator->modelClass, '\\'); ?>;
use <?=ltrim($generator->searchModelClass, '\\'); ?>;
use yii\data\ActiveDataProvider;
use <?=ltrim($generator->baseControllerClass, '\\'); ?>;
use yii\web\HttpException;
use yii\web\VerbFilter;

/**
 * <?=$controllerClass; ?> implements the CRUD actions for <?=$modelClass; ?> model.
 */
class <?=$controllerClass; ?> extends <?=StringHelper::basename($generator->baseControllerClass) . "\n"; ?>
{
	public function behaviors()
	{
		return array(
			'verbs' => array(
				'class' => VerbFilter::className(),
				'actions' => array(
					'delete' => array('post'),
				),
			),
		);
	}

	/**
	 * Lists all <?=$modelClass; ?> models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new <?=$searchModelClass; ?>;
		$dataProvider = $searchModel->search($_GET);

		return $this->render('index', array(
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		));
	}

	/**
	 * Displays a single <?=$modelClass; ?> model.
	 * <?=implode("\n\t * ", $actionParamComments) . "\n"; ?>
	 * @return mixed
	 */
	public function actionView(<?=$actionParams; ?>)
	{
		return $this->render('view', array(
			'model' => $this->findModel(<?=$actionParams; ?>),
		));
	}

	/**
	 * Creates a new <?=$modelClass; ?> model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new <?=$modelClass; ?>;

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(array('view', <?=$urlParams; ?>));
		} else {
			return $this->render('create', array(
				'model' => $model,
			));
		}
	}

	/**
	 * Updates an existing <?=$modelClass; ?> model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * <?=implode("\n\t * ", $actionParamComments) . "\n"; ?>
	 * @return mixed
	 */
	public function actionUpdate(<?=$actionParams; ?>)
	{
		$model = $this->findModel(<?=$actionParams; ?>);

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(array('view', <?=$urlParams; ?>));
		} else {
			return $this->render('update', array(
				'model' => $model,
			));
		}
	}

	/**
	 * Deletes an existing <?=$modelClass; ?> model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * <?=implode("\n\t * ", $actionParamComments) . "\n"; ?>
	 * @return mixed
	 */
	public function actionDelete(<?=$actionParams; ?>)
	{
		$this->findModel(<?=$actionParams; ?>)->delete();
		return $this->redirect(array('index'));
	}

	/**
	 * Finds the <?=$modelClass; ?> model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * <?=implode("\n\t * ", $actionParamComments) . "\n"; ?>
	 * @return <?=$modelClass; ?> the loaded model
	 * @throws HttpException if the model cannot be found
	 */
	protected function findModel(<?=$actionParams; ?>)
	{
<?php
if (count($pks) === 1) {
	$condition = '$id';
} else {
	$condition = array();
	foreach ($pks as $pk) {
		$condition[] = "'$pk' => \$$pk";
	}
	$condition = 'array(' . implode(', ', $condition) . ')';
}
?>
		if (($model = <?=$modelClass; ?>::find(<?=$condition; ?>)) !== null) {
			return $model;
		} else {
			throw new HttpException(404, 'The requested page does not exist.');
		}
	}
}
