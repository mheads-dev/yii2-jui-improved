let mheadsJuiImproved = {
	/**
	 * @param {{id: string, [extraId]: string, [extraInputItemKey]: string, [itemTpl]: string, [autoSearch]: boolean}} params
	 */
	registerAutocomplete: function(params){
		let $searchInput = $('#' + params.id);
		let $extraInput = params.extraId ? $('#' + params.extraId):null;

		$searchInput.on('autocompletecreate', function(event, ui){
			if(params.extraId)
			{
				$searchInput.on('blur', function(){
					if($extraInput.val())
					{
						if($searchInput.val())
						{
							$searchInput.val($searchInput.data('value'));
						}
						else
						{
							$extraInput.val('').trigger('change');
							$searchInput.val('');
							$searchInput.data('value', '');
						}
					}
					else $searchInput.val('');
				});
			}

			if(params.itemTpl)
			{
				$searchInput.autocomplete("instance")._renderItem = function(ul, item){
					let itemTpl = params.itemTpl;

					for(let key in item)
					{
						let value = '';
						if(item.hasOwnProperty(key)) value = item[key];
						itemTpl = itemTpl.replace(new RegExp('\{' + key + '\}', 'g'), value);
					}

					return $("<li>").append(itemTpl).appendTo(ul);
				};
			}

			if(params.autoSearch)
			{
				let autoSearchOn = false;
				$searchInput.on('focus click', function(){
					if(!autoSearchOn && !$searchInput.autocomplete("instance").menu.element.is(':visible'))
					{
						autoSearchOn = true;
						$searchInput.autocomplete('search');
					}
				});
				$searchInput.on('autocompleteresponse', function(){
					setTimeout(function(){
						autoSearchOn = false;
					}, 300);
				});
			}
		});

		$searchInput.on('autocompleteselect', function(event, ui){
			if(params.extraId)
			{
				$extraInput.val(ui.item[params.extraInputItemKey || 'id']).trigger('change');
				$searchInput.data('value', ui.item.value || ui.item.label);
			}
		});
	}
}