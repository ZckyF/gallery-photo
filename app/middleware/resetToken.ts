export default defineNuxtRouteMiddleware((to, from) => {
  const resetToken = useCookie('reset_token');
  // Jika tidak ada reset_token di cookie, redirect ke halaman login
  if (!resetToken.value && to.path !== '/login') {
    return navigateTo('/login');
  }
});
