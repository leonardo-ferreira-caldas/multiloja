var Modal = {
	instances: {},
	defaultOptions: {
		showEffect: 'fadeIn',
		showSpeed: 'fast',
		hideEffect: 'fadeOut',
		hideSpeed: 'fast',
		closeOnOverlayClick: false,
		activeButton: "OK",
		buttons: {
			"OK": function() {
				this.close();
			}
		},
		closeOnKeyUp: [],
		onOpen: function() {},
		onClose: function() {}
	},  
	createNamespace: function() {
	    var randLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));
	    var uniqid = randLetter + Date.now();
	    return uniqid;
	},
	constructor: function(params) {
		
		var $this = $(this);
		var uniqueID = $this.attr("modal-id");
		
		if (typeof Modal.instances[uniqueID] !== 'undefined') {
			if (params == 'show' || typeof params == "undefined") {
				Modal.instances[uniqueID].open();
			} else if (params == 'hide') {
				Modal.instances[uniqueID].close();
			} else if (params == 'destroy') {
				delete Modal.instances[uniqueID];
			} else if (params == "isCreated") {
				return true;
			}
			return;
		} else if (params == "isCreated") {
			return false;
		}
		
		var uniqueID = Modal.createNamespace();
		var options  = $.extend({}, Modal.defaultOptions, params || {})
		$(this).attr("modal-id", uniqueID);
		Modal.instances[uniqueID] = new Modal.functionalities(uniqueID, options);
		Modal.instances[uniqueID].create();
		
		return Modal.instances[uniqueID];
		
	},
	functionalities: function(uniqueID, opts) {
		return {
			options: opts,
			id: uniqueID,
			selector: "[modal-id=" + uniqueID + "]",
			getFunction: function(functionName) {
				return typeof this.options[functionName] == "function" ? this.options[functionName] : function() {};
			},
			create: function() {
				
				var $modal = $(this.selector);
				var $footer = $modal.find(".modal-wrapper-footer");
				 
				$footer.html('');
				
				for (var buttonName in this.options.buttons) {
					var $button = $("<button>", {
						"value": buttonName,
						"text": buttonName
					});
					if (buttonName == this.options.activeButton) {
						$button.addClass("active");
					}
					$button.on("click", this.options.buttons[buttonName]);
					$footer.append($button);
				}
				
			},
			open: function() {
				var scope = this;
				var onOpen = this.getFunction("onOpen");
				var onBeforeOpen = this.getFunction("onBeforeOpen").call(scope);
				if (onBeforeOpen === false) {
					return;
				}
				$(this.selector)[this.options.showEffect](this.options.showSpeed, function() {
					onOpen.call(scope);
				});	
			},
			close: function() {
				var scope = this;
				var onClose = this.getFunction("onClose");

				$(this.selector)[this.options.hideEffect](this.options.hideSpeed, function() {
					onClose.call(scope);
				});	
			},
			destroy: function() {
				if (typeof Modal.instances[this.id] != "undefined") {
					delete Modal.instances[this.id];
				}
			}
		}
	}
};

$.fn.modal = Modal.constructor;