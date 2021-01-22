YUI({
	filter : "raw"
}).use("uploader",function(Y) {
	// TODO: remove in production
	// Y.one("#overallProgress").set("text", "Uploader type: " + // Y.Uploader.TYPE);
	if (Y.Uploader.TYPE != "none" && !Y.UA.ios) {
		bbUploader = {
				init : function() {
					this.uploader = new Y.Uploader({
						width : "150px",
						height : "25px",
						multipleFiles : true,
						appendNewFiles : true,
						swfURL : "index.php?module=Documents&action=multiupload&getswf=1&to_pdf=1&t="+ Math.random(),
						uploadURL : "index.php?module=AOS_Quotes&action=docupload&to_pdf=1",
						simLimit : 1,
						withCredentials : false,
						selectButtonLabel : 'Upload Documents',
						fileFieldName : 'filename',
					});
					
					if (Y.Uploader.TYPE == "html5") {
						this.uploader.set("dragAndDropArea", "body");
						Y.one("#ddmessage").setHTML("<strong>Drag and drop files here.</strong>");
						this.uploader.on([ "dragenter", "dragover" ],function(event) {
							var ddmessage = Y.one("#ddmessage");
							if (ddmessage) {
								ddmessage.setHTML("<strong>Files detected, drop them here!</strong>");
								ddmessage.addClass("yellowBackground");
							}
													
						});
						this.uploader.on([ "dragleave", "drop" ],function(event) {
							var ddmessage = Y.one("#ddmessage");
							if (ddmessage) {
								ddmessage.setHTML("<strong>Drag and drop files here.</strong>");
								ddmessage.removeClass("yellowBackground");
														
							}
													
						});
						
					} else if (Y.Uploader.TYPE == "flash") {
						this.uploader.render("#selectFilesButtonContainer");
						
					} else {
						Y.log("No Flash or HTML5 capabilities detected.");
						
					}
					var resetProposal = false;
					var uploadDone = false;
					this.uploader.render("#selectFilesButtonContainer");
					this.uploader.after("fileselect",function(event) {
						var fileList = event.fileList;							
						var fileTable = Y.one("#filenames tbody");
						if (fileList.length > 0 && Y.one("#nofiles")) {
							Y.one("#nofiles").remove();
							
						}
						if (uploadDone) {
							uploadDone = false;
							fileTable.setHTML("");
							
						}
						Y.each(fileList,function(fileInstance) {
							document.EditView.is_form_updated.value = '1';
							fileTable.append("<tr id='"+ fileInstance.get("id")+ "_row"+ "'>"
									+ "<td class='filename'>"+ fileInstance.get("name")+ "</td>"
									+ "<td class='filesize'>"+ parseFloat(fileInstance.get("size") / 1024).toFixed(2)+ " Kb"+ "</td>"
									+ "<td class='percentdone'>Hasn't started yet</td>");
							});
						
					});
					this.uploader.on("uploadprogress", function(event) {
						var fileRow = Y.one("#"+ event.file.get("id") + "_row");
						fileRow.one(".percentdone").set("text",event.percentLoaded + "%");
						
					});
					this.uploader.on("uploadstart",this.uploader.startUpload);
					this.uploader.on("uploadcomplete",function(event) {
						// console.log('here 1');
						var fileRow = Y.one("#"+ event.file.get("id")+ "_row");
						var responseData = eval('('+ event.data + ')');
						var message = '';
						if (typeof responseData.success != 'undefined' && responseData.success != '') {
							resetProposal = true;
							document.EditView.resetProposal.value = '1';
							document.EditView.is_form_updated.value = '1';
							var doc_ids = document.EditView.doc_id.value;
							if (typeof doc_ids != 'undefined' && doc_ids != '') {
								document.EditView.doc_id.value = doc_ids+ ','+ responseData.success;
								
							} else {
								document.EditView.doc_id.value = responseData.success;
								
							}
							message = 'Finished!!';
							
						} else {
							message = responseData.error;
							
						}
						fileRow.one(".percentdone").set("text",message);
						
					});
					this.uploader.on("totaluploadprogress",function(event) {
						Y.one("#overallProgress").setHTML("Total uploaded: <strong>"
								+ event.percentLoaded+ "%"+ "</strong>");
						
					});
					this.uploader.on("alluploadscomplete",function(event) {
						// alert('No, I will not submit now');
						document.EditView.submit();
						
					});
					
				},
				
				startUpload : function() {
					if (!this.uploadDone && this.uploader.get("fileList").length > 0) {
						this.uploader.set("enabled", false);		
						var perFileVars = {data:  $('#EditView').serialize() };
						this.uploader.set("postVarsPerFile", Y.merge(this.uploader.get("postVarsPerFile"), perFileVars));
						$(".save_button").addClass("yui3-button-disabled");
						$(".save_button").detach("click");
						this.uploader.uploadAll();
						
					}else{
						document.EditView.submit();
						
					}
				}
		}
		bbUploader.init();
		
	} else {
		Y.one("#uploaderContainer").set("text","We are sorry, but to use the uploader, you either need a browser that support HTML5 or have the Flash player installed on your computer.");
		
	}
	
});
