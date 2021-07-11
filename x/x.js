fetch('./x.php?network=dhcp_leases')
  .then((x) => x.json())
  .then((x) => {
    x.forEach((d) => {
      displayDHCP(d);
    })
   });

function displayDHCP(dev) {
  let table = document.getElementById('devices').getElementsByTagName('tbody')[0],
        row = table.insertRow(-1),
    col_mac = row.insertCell(0),
     col_ip = row.insertCell(1),
   col_host = row.insertCell(2),
    col_opt = row.insertCell(3),

    txt_mac = document.createTextNode(dev['mac']),
     txt_ip = document.createTextNode(dev['ip']),
   txt_host = document.createTextNode(dev['host']),
    btn_opt = document.createElement('button');

    btn_opt.setAttribute('class','button');
    btn_opt.setAttribute('onclick','return false;');
    btn_opt.setAttribute('data-mac', dev['mac']);
    btn_opt.innerText = 'details';

    col_mac.appendChild(txt_mac);
     col_ip.appendChild(txt_ip);
   col_host.appendChild(txt_host);
    col_opt.appendChild(btn_opt);
}
