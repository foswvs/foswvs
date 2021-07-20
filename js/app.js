var initr = false,
    conn = document.getElementById('conn'),
    ping = document.getElementById('ping'),
    button = document.getElementById('insert'),
    credits = document.getElementById('credits'),
    mb_limit = document.getElementById('mb_limit'),
    ip_addr = document.getElementById('ip_addr'),
    mac_addr = document.getElementById('mac_addr'),
    waiting = document.getElementById('waiting');

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
  var count = 30, timer = setInterval(function(){ if(button.innerText.toLowerCase()!=='done'){ clearInterval(timer);count=20;} waiting.innerText = `waiting for payment (${count})`;count--;},1000);
}

function device_session() {
  fetch('/api.php')
  .then((res) => res.json() )
  .then((wifi) => {
    initr = wifi.initr;
    ip_addr.innerText = wifi.ip_addr;
    mac_addr.innerText = wifi.mac_addr;
    ping.innerText = `${Math.floor(wifi.ping)}ms`;
    credits.innerText = `${wifi.total_mb_used} / ${wifi.total_mb_limit}`;

    if( initr && wifi.insert_coin ) {
      btnCancelState();

      if( parseFloat(wifi.mb_limit) ) {
        mb_limit.innerText = wifi.mb_limit;
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

    setTimeout(() => device_session(),300);
  });
}

function txnHistory(txn) {
  let table = document.getElementById('txn_history'),
        row = table.insertRow(-1),
    col_amt = row.insertCell(0),
     col_mb = row.insertCell(1),
     col_ts = row.insertCell(2),
    txt_amt = document.createTextNode(txn.amt==0 ? 'FREE' : peso.format(txn.amt)),
     txt_mb = document.createTextNode(format_mb(txn.mb)),
     txt_ts = document.createTextNode(new Date(txn.ts).toLocaleString());

    col_amt.appendChild(txt_amt);
     col_mb.appendChild(txt_mb);
     col_ts.appendChild(txt_ts);
}

function notxn(){
  let table = document.getElementById('txn_history'),
        row = table.insertRow(-1),
      blank = row.insertCell(0);

      blank.setAttribute('colspan', 3);
      blank.appendChild(document.createTextNode('No Transaction History'));
}

function format_mb(size) {
  if( !size ) return size + 'MB';

  let base = Math.floor(Math.log(size) / Math.log(1024));
  let tags = ['MB','GB','TB','PB','EB','ZB','YB'];

  return parseFloat(size / Math.pow(1024, base)).toFixed(2) + tags[base];
}

if( window.location.pathname == '/txn.html' ) {
  fetch('/app.php?do=get_txn').then((x) => x.json()).then((x) => { console.log(x); });
}

if( window.location.pathname == '/' ) {
  device_session();
}
