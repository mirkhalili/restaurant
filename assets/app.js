(()=>{const root=document.documentElement,sidebar=document.querySelector('[data-sidebar]'),backdrop=document.querySelector('[data-sidebar-backdrop]');
const themeButton=document.querySelector('[data-theme-toggle]');
let saved=null;try{saved=localStorage.getItem('restaurant-theme')}catch(e){}
const prefers=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
const applyTheme=theme=>{const next=theme==='dark'?'dark':'light';root.dataset.theme=next;if(themeButton){themeButton.textContent=next==='dark'?'☀':'☾';themeButton.setAttribute('aria-label',next==='dark'?'فعال‌کردن حالت روز':'فعال‌کردن حالت شب');themeButton.setAttribute('title',next==='dark'?'حالت روز':'حالت شب');}};
applyTheme(saved||(prefers?'dark':'light'));
const toggleTheme=()=>{const next=root.dataset.theme==='dark'?'light':'dark';applyTheme(next);try{localStorage.setItem('restaurant-theme',next)}catch(e){}};
if(themeButton)themeButton.addEventListener('click',toggleTheme);
const open=()=>{sidebar?.classList.add('open');backdrop?.classList.add('show')};const close=()=>{sidebar?.classList.remove('open');backdrop?.classList.remove('show')};
document.querySelector('[data-sidebar-open]')?.addEventListener('click',open);backdrop?.addEventListener('click',close);
document.querySelector('[data-sidebar-collapse]')?.addEventListener('click',()=>document.body.classList.toggle('sidebar-collapsed'));
document.addEventListener('keydown',e=>{if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){e.preventDefault();document.querySelector('[data-global-search]')?.focus()}if(e.key==='Escape')close()});
const filterRows=q=>document.querySelectorAll('tbody tr').forEach(row=>{row.hidden=!!q&&!row.textContent.toLowerCase().includes(q)});document.querySelector('[data-global-search]')?.addEventListener('input',e=>filterRows(e.target.value.trim().toLowerCase()));document.querySelector('[data-global-search-local]')?.addEventListener('input',e=>filterRows(e.target.value.trim().toLowerCase()));
})();