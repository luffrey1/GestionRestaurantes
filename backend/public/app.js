// Configuración
const API_URL = '/api/restaurantes';
const API_KEY = 'testkey';

// Utilidades para mostrar mensajes
function showMessage(msg, type = 'success') {
  // Eliminar popup anterior si existe
  const oldPopup = document.getElementById('popup-message-bg');
  if (oldPopup) oldPopup.remove();

  let content = '';
  if (type === 'error' && typeof msg === 'string' && msg.includes('Object(')) {
    const lines = msg.split(/\r?\n|\s*Object\([^)]*\)\./).filter(Boolean);
    const clean = lines.map(line => {
      const m = line.replace(/\(code [^)]+\)/, '').trim();
      return m.replace(/^\w+: /, '');
    }).filter(Boolean);
    content = `<ul class="text-left space-y-1">${clean.map(e => `<li class="flex items-center gap-2"><svg class='w-4 h-4 text-red-400' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M6 18L18 6M6 6l12 12'/></svg> ${e}</li>`).join('')}</ul>`;
  } else {
    const icon = type === 'success'
      ? '<svg class="w-6 h-6 inline mr-2 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
      : '<svg class="w-6 h-6 inline mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    content = icon + msg;
  }

  // Crear popup modal
  const popupBg = document.createElement('div');
  popupBg.id = 'popup-message-bg';
  popupBg.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/40 animate-fadein';
  popupBg.innerHTML = `
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full border ${type === 'success' ? 'border-green-200' : 'border-red-200'} text-center animate-fadein relative">
      <button id="close-popup-message" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition text-2xl font-bold focus:outline-none">&times;</button>
      <div class="mb-2 text-lg font-semibold ${type === 'success' ? 'text-green-700' : 'text-red-700'}">${type === 'success' ? 'Éxito' : 'Error'}</div>
      <div class="mb-2">${content}</div>
    </div>
  `;
  document.body.appendChild(popupBg);
  document.getElementById('close-popup-message').onclick = () => popupBg.remove();
  popupBg.onclick = e => { if (e.target === popupBg) popupBg.remove(); };
}

// Modal
function showModal(html) {
  document.getElementById('modal').innerHTML = html;
  document.getElementById('modal-bg').classList.remove('hidden');
  document.getElementById('modal').classList.remove('hidden');
}
function closeModal() {
  document.getElementById('modal-bg').classList.add('hidden');
  document.getElementById('modal').classList.add('hidden');
}
document.getElementById('modal-bg').onclick = closeModal;

// Loading spinner
function showLoading() {
  document.getElementById('restaurant-list').innerHTML = `
    <div class="flex justify-center items-center py-10">
      <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
    </div>
  `;
}

// Listar restaurantes
async function fetchRestaurants() {
  showLoading();
  try {
    const res = await fetch(API_URL, {
      headers: { 'X-API-KEY': API_KEY }
    });
    if (!res.ok) throw new Error('Error al obtener restaurantes');
    const data = await res.json();
    renderRestaurants(data);
  } catch (e) {
    showMessage(e.message, 'error');
    document.getElementById('restaurant-list').innerHTML = '';
  }
}

function renderRestaurants(restaurantes) {
  const list = document.getElementById('restaurant-list');
  if (!restaurantes.length) {
    list.innerHTML = '<p class="text-center text-gray-400 text-lg italic">No hay restaurantes registrados.</p>';
    return;
  }
  list.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    ${restaurantes.map(r => `
      <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-6 flex flex-col gap-3 hover:scale-[1.03] hover:shadow-2xl transition-transform duration-200 border-t-4 border-primary relative group animate-fadein">
        <span class="absolute top-3 right-4 bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full shadow group-hover:bg-primary/20 transition">ID: ${r.id}</span>
        <div class="flex items-center gap-2 mb-2">
          <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M4 17V5a2 2 0 012-2h12a2 2 0 012 2v12M9 21V9h6v12"/></svg>
          <h2 class="text-xl font-bold text-primary">${r.nombre}</h2>
        </div>
        <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg> <span>${r.direccion}</span></div>
        <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/></svg> <span>${r.telefono}</span></div>
        <div class="flex gap-2 mt-4">
          <button class="view-btn bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1 rounded font-semibold transition group-hover:scale-105" data-id="${r.id}">Ver</button>
          <button class="edit-btn bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded font-semibold transition group-hover:scale-105" data-id="${r.id}">Editar</button>
          <button class="delete-btn bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded font-semibold transition group-hover:scale-105" data-id="${r.id}">Eliminar</button>
        </div>
      </div>
    `).join('')}
  </div>`;

  // Acciones
  document.querySelectorAll('.view-btn').forEach(btn => btn.onclick = () => showDetails(btn.dataset.id));
  document.querySelectorAll('.edit-btn').forEach(btn => btn.onclick = () => showEditForm(btn.dataset.id));
  document.querySelectorAll('.delete-btn').forEach(btn => btn.onclick = () => confirmDelete(btn.dataset.id));
}

// Ver detalles
async function showDetails(id) {
  try {
    const res = await fetch(`${API_URL}/${id}`, { headers: { 'X-API-KEY': API_KEY } });
    if (!res.ok) throw new Error('No se pudo obtener el restaurante');
    const r = await res.json();
    showModal(`
      <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center gap-2"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M4 17V5a2 2 0 012-2h12a2 2 0 012 2v12M9 21V9h6v12"/></svg> ${r.nombre}</h2>
      <div class="mb-2 flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg> <span>${r.direccion}</span></div>
      <div class="mb-4 flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/></svg> <span>${r.telefono}</span></div>
      <button onclick="closeModal()" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow font-bold transition">Cerrar</button>
    `);
  } catch (e) {
    showMessage(e.message, 'error');
  }
}

// Editar restaurante
async function showEditForm(id) {
  try {
    const res = await fetch(`${API_URL}/${id}`, { headers: { 'X-API-KEY': API_KEY } });
    if (!res.ok) throw new Error('No se pudo obtener el restaurante');
    const r = await res.json();
    showModal(`
      <form id="edit-restaurant" class="flex flex-col gap-4">
        <h2 class="text-2xl font-bold text-yellow-700 mb-2 flex items-center gap-2"><svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Editar Restaurante</h2>
        <input name="nombre" value="${r.nombre}" placeholder="Nombre" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white/60 placeholder-gray-400 transition" required />
        <input name="direccion" value="${r.direccion}" placeholder="Dirección" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white/60 placeholder-gray-400 transition" required />
        <input name="telefono" value="${r.telefono}" placeholder="Teléfono" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white/60 placeholder-gray-400 transition" required />
        <div class="flex gap-2">
          <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg shadow font-bold transition active:scale-95" type="submit">Guardar</button>
          <button type="button" onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg shadow font-bold transition active:scale-95">Cancelar</button>
        </div>
      </form>
    `);
    document.getElementById('edit-restaurant').onsubmit = async function(e) {
      e.preventDefault();
      const form = e.target;
      const body = {
        nombre: form.nombre.value,
        direccion: form.direccion.value,
        telefono: form.telefono.value
      };
      try {
        const res = await fetch(`${API_URL}/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-API-KEY': API_KEY
          },
          body: JSON.stringify(body)
        });
        if (!res.ok) {
          const err = await res.json();
          throw new Error(err.message || 'Error al actualizar restaurante');
        }
        showMessage('Restaurante actualizado correctamente');
        closeModal();
        fetchRestaurants();
      } catch (e) {
        showMessage(e.message, 'error');
      }
    };
  } catch (e) {
    showMessage(e.message, 'error');
  }
}

// Eliminar restaurante
function confirmDelete(id) {
  showModal(`
    <h2 class="text-xl font-bold text-red-700 mb-4">¿Eliminar restaurante?</h2>
    <p class="mb-4 text-gray-700">Esta acción no se puede deshacer.</p>
    <div class="flex gap-2">
      <button onclick="deleteRestaurant(${id})" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg shadow font-bold transition active:scale-95">Eliminar</button>
      <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg shadow font-bold transition active:scale-95">Cancelar</button>
    </div>
  `);
}

async function deleteRestaurant(id) {
  try {
    const res = await fetch(`${API_URL}/${id}`, {
      method: 'DELETE',
      headers: { 'X-API-KEY': API_KEY }
    });
    if (!res.ok && res.status !== 204) {
      const err = await res.json();
      throw new Error(err.message || 'Error al eliminar restaurante');
    }
    showMessage('Restaurante eliminado correctamente');
    closeModal();
    fetchRestaurants();
  } catch (e) {
    showMessage(e.message, 'error');
  }
}

// Formulario para crear restaurante
function renderForm() {
  document.getElementById('restaurant-form').innerHTML = `
    <form id="new-restaurant" class="bg-white/80 backdrop-blur p-8 rounded-2xl shadow-xl border-t-4 border-primary mt-8 flex flex-col gap-4 animate-fadein">
      <h2 class="text-2xl font-bold text-primary mb-2 flex items-center gap-2"><svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Nuevo Restaurante</h2>
      <input name="nombre" placeholder="Nombre" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/60 bg-white/60 placeholder-gray-400 transition" required />
      <input name="direccion" placeholder="Dirección" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/60 bg-white/60 placeholder-gray-400 transition" required />
      <input name="telefono" placeholder="Teléfono" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/60 bg-white/60 placeholder-gray-400 transition" required />
      <button class="bg-primary hover:bg-indigo-700 text-white px-6 py-3 rounded-lg shadow font-bold transition active:scale-95" type="submit">Crear</button>
    </form>
  `;
  document.getElementById('new-restaurant').onsubmit = async function(e) {
    e.preventDefault();
    const form = e.target;
    const body = {
      nombre: form.nombre.value,
      direccion: form.direccion.value,
      telefono: form.telefono.value
    };
    try {
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-API-KEY': API_KEY
        },
        body: JSON.stringify(body)
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.message || 'Error al crear restaurante');
      }
      showMessage('Restaurante creado correctamente');
      form.reset();
      fetchRestaurants();
    } catch (e) {
      showMessage(e.message, 'error');
    }
  };
}

// Recargar lista
const reloadBtn = document.getElementById('reload-btn');
if (reloadBtn) reloadBtn.onclick = fetchRestaurants;

// Inicialización
fetchRestaurants();
renderForm(); 