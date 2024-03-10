import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    // Data user
    user: {},
  }),
  getters: {
    // Getter untuk mendapatkan data user
    getUser(state) {
      return state.user;
    },
  },
  actions: {
    // Setter untuk mengubah data user
    setUser(user: object) {
      this.user = user;
    },
    // shouldFetchNewHero() {
    //   // Periksa apakah data imageHero ada dan masa berlakunya sudah lebih dari 3 hari
    //   if (!this.imageHero && !this.lastFetchTimestamp) {
    //     return true;
    //   }

    //   const currentTime = new Date().getTime();
    //   const threeDaysInMillis = 3 * 24 * 60 * 60 * 1000; // 3 hari dalam milidetik
    //   const elapsedTime = currentTime - this.lastFetchTimestamp;

    //   return elapsedTime >= threeDaysInMillis;
    // },
  },
  persist: {
    storage: persistedState.localStorage,
  },
});
