/**
 * Aviso ao sair com alterações não guardadas + rascunho em sessionStorage (opcional).
 * Formulários: adicionar data-unsaved-warn para aviso; data-draft-key="chave" para rascunho.
 */

const DRAFT_PREFIX = 'form_draft_';
const DRAFT_DEBOUNCE_MS = 800;

let formsDirty = new Set();
let draftSaveTimeouts = new Map();

function getFormDraftKey(form) {
  return form.getAttribute('data-draft-key');
}

function markDirty(form) {
  formsDirty.add(form);
  const key = getFormDraftKey(form);
  if (key) scheduleDraftSave(form, key);
}

function markClean(form) {
  formsDirty.delete(form);
}

function scheduleDraftSave(form, key) {
  if (draftSaveTimeouts.has(form)) clearTimeout(draftSaveTimeouts.get(form));
  draftSaveTimeouts.set(
    form,
    setTimeout(() => saveDraft(form, key), DRAFT_DEBOUNCE_MS)
  );
}

function saveDraft(form, key) {
  draftSaveTimeouts.delete(form);
  const data = {};
  const inputs = form.querySelectorAll('input:not([type=file]):not([type=submit]):not([type=button]):not([type=hidden][name=_token]), select, textarea');
  inputs.forEach((el) => {
    if (el.name && el.name !== '_token') {
      if (el.type === 'checkbox' || el.type === 'radio') {
        if (el.checked) data[el.name] = el.value || 'on';
      } else {
        data[el.name] = el.value;
      }
    }
  });
  try {
    sessionStorage.setItem(DRAFT_PREFIX + key, JSON.stringify(data));
  } catch (e) {
    // quota ou privado
  }
}

function loadDraft(key) {
  try {
    const raw = sessionStorage.getItem(DRAFT_PREFIX + key);
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

function clearDraft(key) {
  try {
    sessionStorage.removeItem(DRAFT_PREFIX + key);
  } catch (e) {}
}

function restoreDraftIntoForm(form, data) {
  Object.keys(data).forEach((name) => {
    const els = form.querySelectorAll(`[name="${name}"]`);
    if (!els.length) return;
    const val = data[name];
    els.forEach((el) => {
      if (el.type === 'checkbox' || el.type === 'radio') {
        el.checked = (el.value === val || (el.value === '' && val === 'on'));
      } else {
        el.value = val ?? '';
      }
    });
  });
  markDirty(form);
}

function setupForm(form) {
  const key = getFormDraftKey(form);

  const onInput = () => markDirty(form);
  form.addEventListener('input', onInput);
  form.addEventListener('change', onInput);

  form.addEventListener('submit', () => {
    markClean(form);
    if (key) clearDraft(key);
  });

  // Restaurar rascunho se existir
  if (key) {
    const data = loadDraft(key);
    if (data && Object.keys(data).length > 0) {
      const banner = document.createElement('div');
      banner.setAttribute('role', 'alert');
      banner.className = 'mb-4 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800';
      banner.innerHTML = `
        <span>Tem um rascunho guardado. Deseja restaurar?</span>
        <div class="flex gap-2">
          <button type="button" class="rounded bg-amber-600 px-3 py-1.5 font-medium text-white hover:bg-amber-700 js-draft-restore">
            Restaurar
          </button>
          <button type="button" class="rounded border border-amber-300 bg-white px-3 py-1.5 hover:bg-amber-100 js-draft-discard">
            Descartar
          </button>
        </div>
      `;
      form.insertBefore(banner, form.firstChild);

      banner.querySelector('.js-draft-restore').addEventListener('click', () => {
        restoreDraftIntoForm(form, data);
        banner.remove();
      });
      banner.querySelector('.js-draft-discard').addEventListener('click', () => {
        clearDraft(key);
        banner.remove();
      });
    }
  }
}

function init() {
  document.querySelectorAll('form[data-unsaved-warn]').forEach(setupForm);

  // Um único listener para aviso ao sair
  window.addEventListener('beforeunload', (e) => {
    if (formsDirty.size > 0) {
      e.preventDefault();
      e.returnValue = '';
      return '';
    }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
