$(document).ready( function(){
  var random_id = localStorage.getItem('random_id');
  // random_idがなければ生成して保存
  if(random_id == null) {
    random_id = generateUID(10);
    localStorage.setItem('random_id', random_id);
  }

  $.ajax({
    type: "POST",
    url: "//chienoma.net/user_check.php",
    data: {"random_id": random_id}
  })
  .then(
    function (data) {
      // 1つめは通信成功時のコールバック
    },
      // 2つめは通信失敗時のコールバック
    function (data) {
    }
  );

  function generateUID(len){
    var s = "";
    var a = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z", "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z", "0","1","2","3","4","5","6","7","8","9"];
    for(var i=0; i<len; i++){
      var c = a[Math.floor(a.length*Math.random())];
      s = s + c.toString();
    }
    return s;
  }
});
