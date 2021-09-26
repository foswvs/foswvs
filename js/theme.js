function sw_theme(){
  let t = get_theme() !== 'dark' ? 'dark' : 'light';
  document.cookie = `theme=${t}; max-age=31536000; path=/a/`;
  set_theme();
}
function get_theme() {
  let c = document.cookie.split('; ');
  if(  c.some(a=>a.startsWith('theme')) ) {
    return c.find(a=>a.startsWith('theme')).split('=').pop();
  }
}
function set_theme() {
  document.documentElement.dataset.theme=get_theme();
}
set_theme();
