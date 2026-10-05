export default {
  bind(el, binding, vnode) {
    // Handler for click outside event
    el.__clickOutsideHandler__ = (event) => {
      const vm = vnode.context;
      const lastToggleTarget = vm?.lastToggleClickTarget;
      // Check if the click target is not inside the element itself
      if (
        !(el === event.target || el.contains(event.target)) &&
        event.target !== lastToggleTarget // Ignore the bell icon click
      ) {
        binding.value(event);
      }
    };

    // Listen for mousedown events on the document
    document.addEventListener('mousedown', el.__clickOutsideHandler__);
  },
  unbind(el) {
    // Clean up the event listener when the directive is unbound
    document.removeEventListener('mousedown', el.__clickOutsideHandler__);
  }
};
