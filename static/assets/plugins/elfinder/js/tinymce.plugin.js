  var FileBrowserDialogue = {
    init: function() {
      // Here goes your code for setting your custom things onLoad.
    },
    mySubmit: function (file) {
      // pass selected file data to TinyMCE
      parent.tinymce.activeEditor.windowManager.getParams().oninsert(file);
      // close popup window
      parent.tinymce.activeEditor.windowManager.close();
    }
  }

  $().ready(function() {
    var elf = $('#elfinder').elfinder({
      // set your elFinder options here
      url: rootPath + 'staff/connector/',  // connector URL
      getFileCallback: function(file) { // editor callback
        // file.url - commandsOptions.getfile.onlyURL = false (default)
        // file     - commandsOptions.getfile.onlyURL = true (best with this alternative code)
        FileBrowserDialogue.mySubmit(file); // pass selected file path to TinyMCE 
      }
    }).elfinder('instance');      
  });