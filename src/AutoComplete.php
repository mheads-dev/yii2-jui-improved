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

class AutoComplete extends \yii\jui\AutoComplete
{
	/**
	 * @var string
	 *
	 * Specifies an arbitrary element output template. You can use wildcard keys of the form «{keyname}».
	 * For example you could write the following in your widget configuration:
	 *
	 * ```php
	 * 'clientRenderItemTpl' => '<div>{label}<br><small>{description}</small><div>',
	 * ```
	 */
	public $clientRenderItemTpl;

	/**
	 * @var bool
	 */
	public $useExtraInput = false;

	/**
	 * @var string
	 * 
	 * useExtraInput must be true
	 */
	public $extraInputItemKey = 'id';

	/**
	 * @var string
	 *
	 * useExtraInput must be true
	 */
	public $displayInputValue = '';

	protected function registerClientEvents($name, $id)
	{
		parent::registerClientEvents($name, $id);
		$this->registerClientRenderItemTpl($id);
		$this->registerClientHiddenInputEvents($id);
	}

	protected function registerClientRenderItemTpl($id)
	{
		if($this->clientRenderItemTpl)
		{
			$js = <<< JS
jQuery('#$id').on('autocompletecreate', function(event, ui){
	jQuery(event.target).autocomplete("instance")._renderItem = function(ul, item){
		let itemTpl = '{$this->clientRenderItemTpl}';
		
		for (let key in item)
		{
			let value = '';
			if(item.hasOwnProperty(key)) value = item[key];
			itemTpl = itemTpl.replace(new RegExp('\{' + key + '\}', 'g'), value);	
		}
		
		return jQuery("<li>").append(itemTpl).appendTo(ul);
	};
});
JS;
			$this->getView()->registerJs($js);
		}
	}

	protected function registerClientHiddenInputEvents($id)
	{
		if($this->useExtraInput)
		{
			$js = <<< JS
jQuery('#$id').on('autocompleteselect', function(event, ui){
	let labelInput = $(event.target);
	let valueInput = $('#$id-value');

	valueInput.val(ui.item['{$this->extraInputItemKey}']).trigger('change');
	labelInput.data('value', ui.item.value || ui.item.label);
});
jQuery('#$id').on('autocompletecreate', function(event, ui){
	let labelInput = $(event.target);
	let valueInput = $('#$id-value');
	
	labelInput.on('blur', function(){
		if(valueInput.val())
		{
			if(labelInput.val())
			{
				labelInput.val(labelInput.data('value'));
			}
			else
			{
				valueInput.val('').trigger('change');
				labelInput.val('');
				labelInput.data('value', '');
			}
		}
		else labelInput.val('');
	});
});
JS;
			$this->getView()->registerJs($js);
		}
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