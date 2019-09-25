<?php
/**
 * Created by PhpStorm.
 * User: Alexeenko Sergey Aleksandrovich
 * Phone: +79231421947
 * Email: sergei_alekseenk@list.ru
 * Company: http://machineheads.ru
 * Date: 24.09.2019
 * Time: 14:26
 */

namespace mheads\jui;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class AutoComplete extends \yii\jui\AutoComplete
{
	/**
	 * @var string
	 *
	 * Определяет произвольный шаблона вывода элемента. Вы можете использовать подстановочные ключи вида «{имя ключа}».
	 * Например, вы можете написать следующее в конфигурации вашего виджета:
	 *
	 * ```php
	 * 'clientRenderItemTpl' => '<div>{label}<br><small>{description}</small><div>',
	 * ```
	 */
	public $clientRenderItemTpl;

	/**
	 * @var bool
	 * 
	 * Включает автоматический запуск поиска при фокусе на input
	 */
	public $autoSearch = false;

	/**
	 * @var bool
	 * 
	 * Активирует добавление скрытого инпута в которое пишется id значения, подтянутого из search items.
	 * Часто нужно передавать не label или value, а совершенно иное значение на бекенд.
	 */
	public $useExtraInput = false;

	/**
	 * @var string
	 * 
	 * useExtraInput must be true
	 * Указывает из какого поля брать значение в search items для подстановки в ExtraInput
	 */
	public $extraInputItemKey = 'id';

	/**
	 * @var string
	 *
	 * useExtraInput must be true
	 * Это значение вставляется в поле, которое отображается пользователю
	 */
	public $displayInputValue = '';

	protected function registerClientEvents($name, $id)
	{
		parent::registerClientEvents($name, $id);
		$this->registerClientJs($id);
	}

	protected function registerClientJs($id)
	{
		$registerParams = [
			'id'      => $id,
			'itemTpl' => $this->clientRenderItemTpl,
		];

		if($this->useExtraInput)
		{
			$registerParams['extraId'] = $id."-value";
			$registerParams['extraInputItemKey'] = $this->extraInputItemKey;
		}

		if($this->autoSearch)
		{
			$registerParams['autoSearch'] = true;
		}

		$registerParams = Json::encode($registerParams);
		$this->getView()->registerJs('mheadsJuiImproved.registerAutocomplete('.$registerParams.');');
	}

	public function registerWidget($name, $id = NULL)
	{
		parent::registerWidget($name, $id);
		Asset::register($this->view);
	}

	/**
	 * Renders the AutoComplete widget.
	 * @return string the rendering result.
	 */
	public function renderWidget()
	{
		$content = '';

		if(!$this->useExtraInput)
		{
			$content = parent::renderWidget();
		}
		else
		{
			if($this->hasModel())
			{
				$content = Html::activeHiddenInput($this->model, $this->attribute, ArrayHelper::merge($this->options, [
					'id' => $this->options['id'].'-value'
				]));
			}
			else
			{
				$content = Html::hiddenInput($this->name, $this->value, ArrayHelper::merge($this->options, [
					'id' => $this->options['id'].'-value'
				]));
			}

			$content .= Html::textInput(NULL, $this->displayInputValue, ArrayHelper::merge($this->options, [
				'data' => ['value' => $this->displayInputValue]
			]));
		}

		return $content;
	}
}