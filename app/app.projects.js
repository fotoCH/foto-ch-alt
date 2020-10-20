app.controller('ProjectsController', [
  '$scope',
  '$http',
  '$rootScope',
  '$window',
  '$state',
  '$timeout',
  function($scope, $http, $rootScope, $window, $state, $timeout) {
    $scope.projects = [];

    // get projects
    $scope.updateProjectList = function() {
      $scope.projects = [];
      $http
        .get($rootScope.ApiUrl + '/?a=projects&type=list')
        .success(function(data) {
          $timeout(function() {
            $scope.projects = data;
          }, 0);
        });
    };
    $scope.updateProjectList();
  }
]);

app.controller('ProjectDetailController', [
  '$scope',
  '$http',
  '$rootScope',
  '$window',
  '$state',
  '$timeout',
  '$stateParams',
  '$sce',
  function(
    $scope,
    $http,
    $rootScope,
    $window,
    $state,
    $timeout,
    $stateParams,
    $sce
  ) {
    $http
      .get(
        $rootScope.ApiUrl +
          '/?a=projects&type=get&title=' +
          $stateParams.project_identification
      )
      .success(function(data) {
        $scope.detail = data;
        $scope.detail.text = $sce.trustAsHtml($scope.detail.text);
      });
  }
]);

function initTinyMce() {
  var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

  tinymce.init({
    selector: 'textarea#projectText',
    plugins:
      'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable',
    /* tinydrive_token_provider: 'URL_TO_YOUR_TOKEN_PROVIDER',
     * tinydrive_dropbox_app_key: 'YOUR_DROPBOX_APP_KEY',
     * tinydrive_google_drive_key: 'YOUR_GOOGLE_DRIVE_KEY',
     * tinydrive_google_drive_client_id: 'YOUR_GOOGLE_DRIVE_CLIENT_ID', */
    /* mobile: {
     *   plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker textpattern noneditable help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable'
     * }, */
    menu: {
      tc: {
        title: 'TinyComments',
        items: 'addcomment showcomments deleteallconversations'
      }
    },
    menubar: 'file edit view insert format tools table tc help',
    toolbar:
      'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
    /* autosave_ask_before_unload: true,
     * autosave_interval: '30s',
     * autosave_prefix: '{path}{query}-{id}-',
     * autosave_restore_when_empty: false,
     * autosave_retention: '2m', */
    image_advtab: true,
    /* link_list: [
     *   { title: 'My page 1', value: 'http://www.tinymce.com' },
     *   { title: 'My page 2', value: 'http://www.moxiecode.com' }
     * ],
     * image_list: [
     *   { title: 'My page 1', value: 'http://www.tinymce.com' },
     *   { title: 'My page 2', value: 'http://www.moxiecode.com' }
     * ],
     * image_class_list: [
     *   { title: 'None', value: '' },
     *   { title: 'Some class', value: 'class-name' }
     * ], */
    importcss_append: true,
    templates: [
      {
        title: 'Lead text',
        description: 'Einführungstext (grosse Schrift)',
        content: '<div class="mceTmpl"><p class="lead">Lead Text</p></div>'
      },
      {
        title: 'Autor Box',
        description: 'Graue Box',
        content:
          '<div class="mceTmpl"><div class="slim box"><p>Markus Sch&uuml;rpf</p></div></div>'
      }
    ],
    template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
    template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
    height: 600,
    image_caption: true,
    quickbars_selection_toolbar:
      'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_noneditable_class: 'mceNonEditable',
    toolbar_mode: 'sliding',
    /* spellchecker_whitelist: ['Ephox', 'Moxiecode'], */
    tinycomments_mode: 'embedded',
    content_style: '.mymention{ color: gray; }',
    contextmenu: 'link image imagetools table configurepermanentpen',
    a11y_advanced_options: true,
    skin: useDarkMode ? 'oxide-dark' : 'oxide',
    content_css: useDarkMode ? 'dark' : 'default'
    /*
    The following settings require more configuration than shown here.
    For information on configuring the mentions plugin, see:
    https://www.tiny.cloud/docs/plugins/mentions/.
  */
    /* mentions_selector: '.mymention',
     * mentions_fetch: mentions_fetch,
     * mentions_menu_hover: mentions_menu_hover,
     * mentions_menu_complete: mentions_menu_complete,
     * mentions_select: mentions_select */
  });
}

app.controller('ManageProjectsCtrl', [
  '$scope',
  '$http',
  '$rootScope',
  '$state',
  '$timeout',
  function($scope, $http, $rootScope, $state, $timeout) {
    initTinyMce();

    $scope.formData = {};
    $scope.projects = [];
    $scope.formTitle = '';
    $scope.formActionValue = $rootScope.translations.create;

    $timeout(function() {
      if (!$rootScope.manageProjectsAllowed()) {
        $state.go('home');
      }
      $scope.formTitle = $rootScope.translations.createProject;
    }, 0);

    $scope.prepareCreateForm = function() {
      $scope.formTitle = $rootScope.translations.create;
      $scope.formActionValue = $rootScope.translations.create;
      $scope.formData = {};
    };

    // get projects
    $scope.updateProjectList = function() {
      $scope.projects = [];
      $http
        .get($rootScope.ApiUrl + '/?a=projects&type=list')
        .success(function(data) {
          $timeout(function() {
            $scope.projects = data;
          }, 0);
        });
    };
    $scope.updateProjectList();

    $scope.editProject = function(id) {
      $scope.createProjectToggle = true;
      $scope.formTitle = $rootScope.translations.edit;
      $scope.formActionValue = $rootScope.translations.update;

      $http
        .get($rootScope.ApiUrl + '/?a=projects&type=get&id=' + id)
        .success(function(data) {
          $timeout(function() {
            $scope.formData = data;
            tinymce.get('projectText').setContent($scope.formData.text);
            if ($scope.formData.published == 1) {
              $scope.formData.published = true;
            }
          }, 0);
        });
    };

    $scope.deleteProject = function(id, name) {
      if (confirm('Delete Project ' + name + '?')) {
        $http
          .get($rootScope.ApiUrl + '/?a=projects&type=delete&id=' + id)
          .success(function(data) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          });
      }
    };

    $scope.processForm = function() {
      $scope.formData.text = tinymce.get('projectText').getContent();
      if (typeof $scope.formData.id === 'undefined') {
        $http({
          method: 'POST',
          url: $rootScope.ApiUrl + '/?a=projects&type=create',
          data: $scope.formData
        }).then(
          function(response) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          },
          function(response) {
            console.log(response);
          }
        );
      } else {
        $http({
          method: 'POST',
          url: $rootScope.ApiUrl + '/?a=projects&type=update',
          data: $scope.formData
        }).then(
          function(response) {
            $timeout(function() {
              $scope.updateProjectList();
            }, 0);
          },
          function(response) {
            console.log(response);
          }
        );
      }
    };
  }
]);
