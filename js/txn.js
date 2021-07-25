const peso = Intl.NumberFormat('en-US', {style: 'currency', currency: 'PHP'});

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

function getDeviceTxn() {
  fetch('/api/index.php?do=get_txn').then((x) => x.json()).then((x) => { x.forEach((tx) => { tx.ts ? txnHistory(tx) : notxn(); }); });
}

if( window.location.pathname == '/txn.html' ) {
  getDeviceTxn();
}

