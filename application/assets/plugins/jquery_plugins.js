(function($) {
	
	$.fn.serializeJSON = function() {
		var obj = {};
		
		this.each(function() {
			$(this).find("input, select, textarea").each(function() {
				obj[$(this).attr("name") || $(this).attr("id")] = $(this).val();
			});
		});
		
		return obj;
	};
	
	$.fn.deserializeJSON = function(obj) {

		return this.each(function() {
			$(this).find("input, select, textarea").each(function() {
				var name = $(this).attr("name");
				if (typeof obj[name] != "undefined") {
					$(this).val(obj[name]);
				}
			});
		});

	};
	
	$.fn.loadFormObject = function() {
		if (typeof dataFormObj != "undefined") {
			return this.each(function() {
				$(this).deserializeJSON(dataFormObj);
			});
		}

		return null;
	};
	
	$.fn.onRequest = function(callback) {
		var ajax = null;
		
		return this.each(function() {
			var $form = $(this);
		
			$form.on("submit", function() {
				$.ajax({
					url: $form.attr("action"),
					data: $form.serializeJSON(),
					dataType: 'json',
					method: $form.attr("method") || "POST",
					success: callback
				});
				
				return false;
			});
		
		});
	};
	
	$.fn.sendRequest = function(callback) {
		return this.each(function() {
			var $form = $(this);
			$form.onRequest(callback);
			$form.submit();
			$form.off("submit");
		});
	};
	
	$.fn.isEmpty = function() {
		var isEmptyValues = [];
		
		this.each(function() {
			isEmptyValues.push($(this).val());
		});
		
		return Utils.helper.isEmpty.apply(this, isEmptyValues);
		
	};
	
	$.fn.clearForm = function() {
		$(this).find("input, textarea").val('');
		$(this).find("select").each(function() {
			$(this).val($(this).data("default"));
		});
	};
	
}(jQuery));