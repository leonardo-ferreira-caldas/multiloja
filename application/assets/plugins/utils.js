var Utils = {
	init: function() {
		
		$.prompt.setDefaults({
			show: 'fadeIn',
			hide: 'fadeOut',
			overlayspeed: 'fast',
			promptspeed: 10,
			closeText: 'X'
		});
		
	},
	ajax: {
		action: function(url, data, funcCallback) {
			
			$.ajax({
				url: url,
				data: data,
				dataType: 'json',
				method: "POST",
				success: function(callback) {
					if (callback.success) {
						if (typeof funcCallback == "function") {
							funcCallback(callback.response);
						}
					} else {
						Utils.alert.warn(callback.response);
					}
				},
				statusCode: {
					404: function() {
						Utils.alert.warn("A página '" + url + "' não foi encontrada.");
					},
					403: function() {
						Utils.alert.warn("Acesso não permitido.<br>Página '" + url + "'");
					}
				}
			});
			
		},
		service: function(url, data, callback) {
			data = data || []; 
			Utils.ajax.action('service/json/' + url, {"args": data}, callback);
		}
	},
	url: {
		redirect: function(url) {
			window.location.href = url;
		}
	},
	grid: {

	},
	form: {
		loadSelects: function() {
			
			var $selects = $(".select-autoload");
			
			$selects.each(function() {
				var $this = $(this);
				var url  = $this.data("url");
				var key  = $this.data("key");
				var desc = $this.data("desc");
				
				Utils.ajax.service(url, [], function(callback) {
					for (var x in callback) {
						$this.append($("<option>", {
							value: callback[x][key],
							text: callback[x][desc]
						}));
					}
				});
			});
			
		},
		sendFormPost: function(url, sendData) {
			
			if (Utils.helper.isEmpty(url, sendData)) {
				alert("Algum parametro informado para a funcao Utils.form.sendFormPost está vazio.");
				return;
			}	
			
			var $form = $("<form />", {
				action: url,
				method: "POST"
			});
			
			for (var idx in sendData) {
			
				$form.append($("<input />", {
					type: "hidden",
					value: sendData[idx],
					name: idx
				}));
			
			}
			
			$form.submit();
			
		}
	},
	modal: {
		form: function(modalId, submitFnc, beforeOpenFnc) {
			
			if ($(modalId).modal("isCreated")) {
				$(modalId).modal('destroy');
			}
			
			$(modalId).modal({
				activeButton: "Salvar",
				buttons: {
					"Salvar": submitFnc,
					"Cancelar": function() {
						$(modalId).modal('hide');
					}
				},
				onBeforeOpen: beforeOpenFnc
			});
			
			$(modalId).modal('show');
			
		}
	},
	alert: {
		info: function(message, funcOk) {
			var title = "<img src='application/assets/img/info_icon.png' /> Informação";
			
			$.prompt(message, {
				title: title,
				buttons: { "OK": true},
				submit: function(e,v,m,f){
					if (v) {
						if (typeof funcOk == "function") {
							funcOk.call();
						}
					}
				}
			});
		},
		warn: function(message, funcOk) {
			var title = "<img src='application/assets/img/warning_icon.png' /> Erro";
			
			$.prompt(message, {
				title: title,
				buttons: { "OK": true},
				submit: function(e,v,m,f){
					if (v) {
						if (typeof funcOk == "function") {
							funcOk.call();
						}
					}
				}
			});
		},
		confirm: function(message, funcOk, funcCanc) {
			
			var title = "<img src='application/assets/img/warning_icon.png' width='26' style='margin: 0px 5px 2px 5px'/> <b>Cuidado!</b>";
			
			$.prompt(message, {
				title: title,
				buttons: { "Confirmar": true, "Cancelar": false},
				submit: function(e,v,m,f){
					if (v) {
						if (typeof funcOk == "function") {
							funcOk.call();
						}
					} else {
						if (typeof funcCanc == "function") {
							funcCanc.call();
						}
					}
				}
			});
			
		}
	},
	helper: {
		isEmpty: function() {
			var isEmpty = false;
			
			for (var index in arguments) {
				var valor = arguments[index];
				if ( valor === null || $.trim(valor) == "" || typeof valor == "undefined" || valor == undefined || valor.toString().length == 0) {
					isEmpty = true;
					break;
				}
			}
			
			return isEmpty;
		},
		formatarCPF: function(CPF) {
			return CPF.substring(0, 3) + "." + CPF.substring(3, 6) + "." + CPF.substring(6, 9) + "-" + CPF.substring(9, 11);
		},
		formatarCNPJ: function(CNPJ) {
			return CNPJ.substring(0, 2) + "." + CNPJ.substring(2, 5) + "." + CNPJ.substring(5, 8) + "/" + CNPJ.substring(8, 12) + "-" + CNPJ.substring(12, 14);
		}
	}
};

Utils.init();