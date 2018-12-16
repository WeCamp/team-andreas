var app = new Vue({
  el: '#app',
  data: {
    fileResults: [{
      fileName: 'query.php',
      coverage: '100%'
    },{
      fileName: 'index.php',
      coverage: '?'
    },{
      fileName: 'doc.php',
      coverage: '0%'
    }],
    results: {
      percentage: '50%',
      'filesChecked': '3',
      'filesCovered': '1',
      'filesInconclusive': '1',
      'filesFailed': '1'
    }
  }
});
