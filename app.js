var initr = false,
    conn = document.getElementById('conn'),
    ping = document.getElementById('ping'),
    button = document.getElementById('insert'),
    credits = document.getElementById('credits'),
    mb_limit = document.getElementById('mb_limit'),
    ip_addr = document.getElementById('ip_addr'),
    mac_addr = document.getElementById('mac_addr'),
    waiting = document.getElementById('waiting');

setInterval(() => {
  fetch('/api.php')
  .then((res) => res.json() )
  .then((wifi) => {
    initr = wifi.initr;
    ip_addr.innerText = wifi.ip_addr;
    mac_addr.innerText = wifi.mac_addr;
    ping.innerText = `${Math.floor(wifi.ping)}ms`;
    credits.innerText = `${wifi.total_mb_used} / ${wifi.total_mb_limit}MB`;

    if( initr && wifi.insert_coin ) {
      btnCancelState();

      if( wifi.mb_limit ) {
        mb_limit.innerText = wifi.mb_limit + 'MB';
        mb_limit.style.display = 'block';
      }
    } else {
      btnInsertState();
    }

    if( wifi.connected ) {
      conn.innerText = 'connected';
    }
    else {
      conn.innerText = 'disconnected';
    }
  });
},3000);

button.addEventListener('click', function(e) {
  btn = e.target;

  if( !initr ) {
    alert("Coinslot is not ready.");
    return false;
  }

  if( btn.innerText.toLowerCase() !== 'done' ) {
    fetch('/api.php?do=topup');
    btnCancelState();
  }
  else {
    fetch('/api.php?do=topup_cancel');
    btnInsertState();
  }
});

function btnInsertState() {
  if( button.innerText.toLowerCase() == 'done' ) {
    button.innerText = 'insert coin';
    button.style.borderColor = '#11aa11';
    button.style.backgroundColor = '#22aa22';
    waiting.style.display = 'none';
    mb_limit.style.display = 'none';
  }
}

function btnCancelState() {
  if( button.innerText.toLowerCase() !== 'done' ) {
    button.innerText = 'done';
    button.style.borderColor = '#aa1111';
    button.style.backgroundColor = '#aa2222';
    waiting.style.display = 'block';
    payment_count_down();
  }
}

function payment_count_down() {
  /* fix for https://github.com/ligrevx/foswvs-php/blob/7405c0d747b98a6d5fc2310a97a1b1eea351981d/app.js#L66 */
  var count = 20, timer = setInterval(function(){ if(button.innerText.toLowerCase()!=='done'){ clearInterval(timer);count=20;} waiting.innerText = `waiting for payment (${count})`;count--;},1000);
}
