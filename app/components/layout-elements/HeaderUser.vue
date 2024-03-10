<template>
  <header class="header-user">
    <row class="box-header">
      <div class="img-user">
        <v-skeleton-loader class="mt-16 mb-4" v-if="skeletonLoader" type="avatar" style="background-color: transparent"></v-skeleton-loader>
        <v-avatar v-else color="#90A4AE" class="mt-16 mb-4" :image="`${urlAvatar}${dataUser.avatar_name}`" loading="lazy" size="130"></v-avatar>
      </div>

      <div v-if="skeletonLoader">
        <v-skeleton-loader style="background-color: transparent" type="list-item-three-line" width="200"></v-skeleton-loader>
      </div>
      <div class="username-bio" v-else>
        <h1>{{ dataUser.username }}</h1>
        <h4>{{ dataUser.full_name }}</h4>
        <p>{{ dataUser.bio }}</p>
      </div>
      <div class="button mt-4">
        <v-btn v-if="store.getUser.id !== dataUser.id && !isFollowed" class="mx-2 text-none gradient-button" rounded :loading="loadingFollow" @click="followUser(dataUser.id)"> Follow </v-btn>
        <v-btn v-if="store.getUser.id !== dataUser.id && isFollowed" class="mx-2 text-none unfollow-botton" rounded variant="outlined" @click="unfollowUser(dataUser.id)" :loading="loadingFollow"> Unfollow </v-btn>
        <v-btn v-if="store.getUser.id == dataUser.id" class="mx-2 text-none edit-profile-button" variant="tonal" rounded to="/settings/profile"> Settings </v-btn>

        <!-- <v-btn icon="mdi-cog-outline " variant="outlined" size="40" class="mx-2 text-none settings" outlined> </v-btn> -->
      </div>
      <div class="amount-follow mt-4">
        <!-- Jumlah Pengikut -->
        <div
          @click="
            dialogFollow = true;
            dataDialog = dataFollowers;
            titleDialog = 'Followers';
          "
          style="cursor: pointer"
        >
          <span class="number">{{ convertFollow(dataFollowers.length) }}</span> Followers
        </div>
        <!-- Jumlah yang Diikuti -->
        <div
          @click="
            dialogFollow = true;
            dataDialog = dataFollowing;
            titleDialog = 'Following';
          "
          style="cursor: pointer"
        >
          <span class="number">{{ convertFollow(dataFollowing.length) }}</span> Following
        </div>
      </div>
    </row>
    <v-dialog v-model="dialogFollow" transition="dialog-top-transition" scrollable max-width="400">
      <v-card>
        <v-card-title>{{ titleDialog }}</v-card-title>
        <v-divider></v-divider>
        <v-card-text style="height: 300px">
          <v-list class="list-follow">
            <v-list-item v-for="(follow, index) in dataDialog" :key="'skeleton-' + index">
              <template v-slot:prepend>
                <v-avatar to="/" size="44" :image="`${urlAvatar}${titleDialog !== 'Followers' ? follow.follower.avatar_name : follow.following.avatar_name}`" class="avatar"></v-avatar>
              </template>
              <template v-slot:title>
                <NuxtLink class="username" :to="`/users/${titleDialog !== 'Followers' ? follow.follower.username : follow.following.username}`">
                  {{ titleDialog !== 'Followers' ? follow.follower.username : follow.following.username }}
                </NuxtLink>
              </template>
              <template v-slot:append v-if="titleDialog == 'Followers' && username == store.getUser.username">
                <div
                  class="reply-button"
                  style="color: #d32f2f; font-size: 14px; cursor: pointer"
                  @click="
                    dialogDelete = true;
                    selectedFollowerId = follow.id;
                  "
                >
                  Delete
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="justify-end">
          <v-btn
            class="text-none"
            variant="text"
            @click="
              dialogFollow = false;
              dataDialog = [];
            "
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" transition="dialog-top-transition" max-width="400">
      <v-card>
        <v-card-text class="pa-8 card-text-dialog">
          <span class="mx-auto icon-alert">
            <v-icon size="60">mdi-delete</v-icon>
          </span>

          <div class="text-center text-delete mt-5">
            <h3>Do you want to delete this follower ?</h3>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn variant="text" class="text-none" @click="deleteSubmit" :loading="loadingDelete">Yes</v-btn>
          <v-btn variant="text" class="text-none" @click="dialogDelete = false" :disabled="loadingDelete">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </header>
</template>

<script setup lang="ts">
import axios from 'axios';
import { useUserStore } from '~/stores/store';
import { convertFollow } from '~/helpers/convert';

const store = useUserStore();

const { username } = useRoute().params;

let formData = ref<object>({
  follower_id: '',
});
let dataDialog = ref<[]>([]);
let titleDialog = ref<string>('');

const dataUser = ref<object>({});
const dataFollowers = ref<[]>([]);
const dataFollowing = ref<[]>([]);
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const url = useRuntimeConfig().public.URL_API;
const cookie = useCookie('auth_token');
let loadingFollow = ref<boolean>(false);
let skeletonLoader = ref<boolean>(true);
let dialogFollow = ref<boolean>(false);
let dialogDelete = ref<boolean>(false);
let loadingDelete = ref<boolean>(false);
let selectedFollowerId = ref<string>('');

const handleDialogOpen = (newOpenValue: boolean) => {
  dialogFollow.value = newOpenValue;
};
let isFollowed = computed(() => {
  return dataFollowers.value.some((follow) => follow.following.id === store.getUser.id);
});

const followUser = async (userId: string) => {
  try {
    loadingFollow.value = true;
    formData.value.follower_id = userId;
    console.log(formData);
    // Kirim permintaan ke backend untuk mengikuti pengguna
    const response = await axios.post(`${url}/follows`, formData.value, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    const { data: followsData } = await useFetch(`${url}/follows/${username}/detail`, {});
    dataFollowers.value = followsData.value.data.followers;
    dataFollowing.value = followsData.value.data.following;
  } catch (error) {
    console.error('Error following user:', error);
  } finally {
    loadingFollow.value = false;
  }
};

const unfollowUser = async (userId: string) => {
  try {
    loadingFollow.value = true;
    const response = await axios.delete(`${url}/follows/${userId}`, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });
    const { data: followsData } = await useFetch(`${url}/follows/${username}/detail`, {});
    dataFollowers.value = followsData.value.data.followers;
    dataFollowing.value = followsData.value.data.following;
    console.log(response);
  } catch (error) {
    console.error('Error unfollowing user:', error);
  } finally {
    loadingFollow.value = false;
  }
};
const deleteSubmit = async () => {
  try {
    loadingDelete.value = true;
    console.log(selectedFollowerId);

    // Kirim permintaan untuk menambahkan like
    const response = await axios.delete(`${url}/follows-delete-by-id/${selectedFollowerId.value}`, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });

    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response.data.message);
    // Perbarui data likes setelah like berhasil ditambahkan
    const { data: followsData } = await useFetch(`${url}/follows/${username}/detail`);
    dataFollowers.value = followsData.value.data.followers;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding like:', error);
  } finally {
    loadingDelete.value = false;
    dialogDelete.value = false;
  }
};

try {
  const { data: userDetail } = await useFetch(`${url}/user-detail/${username}`);

  dataUser.value = userDetail.value.data;
} catch (error) {
  console.error('error', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}
try {
  const { data: followsData } = await useFetch(`${url}/follows/${username}/detail`);

  dataFollowers.value = followsData.value.data.followers;
  dataFollowing.value = followsData.value.data.following;
} catch (error) {
  console.error('error', error);
} finally {
  setTimeout(() => {
    skeletonLoader.value = false;
  }, 500);
}
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/components/buttons';
.header-user {
  //   background-color: rgb(230, 230, 230);
  display: flex;
  justify-content: center;

  .box-header {
    display: flex;
    align-items: center;
    flex-direction: column;
    max-width: 750px;
    text-align: center;
    .username-bio {
      h1 {
        color: variables.$primary-black;
        font-weight: 500;
        font-size: 3rem;
      }
      h4 {
        color: variables.$primary-black;
        font-weight: 500;
      }
      p {
        color: variables.$secondary-black;
      }
    }

    .button {
      .gradient-button {
        font-size: 1rem;
      }
      .edit-profile-button {
        font-size: 1rem;
        letter-spacing: normal;
      }
      .unfollow-botton {
        letter-spacing: normal;
        color: variables.$primary-black;
        font-size: 1rem;
      }

      .settings {
        border-color: variables.$primary-black;
        color: variables.$primary-black;
      }
    }

    .amount-follow {
      display: inline-flex;
      gap: 50px;

      div {
        .number {
          font-weight: 600;
        }
        color: variables.$primary-black;
        font-weight: 500;
      }
    }
  }
}

.list-follow {
  .avatar {
    transition: all 0.2s ease;
    cursor: pointer;
    &:hover {
      filter: brightness(70%);
    }
  }
  .username {
    text-decoration: none;
    color: variables.$primary-black;
    font-weight: 500;
    font-size: 1rem;

    &:hover {
      color: variables.$secondary-black;
    }
  }
}
.card-text-dialog {
  .icon-alert {
    display: flex;
    justify-content: center;
    color: variables.$primary-black;
    font-weight: 500;
  }

  .text-delete {
    h3 {
      color: variables.$primary-black;
      font-weight: 500;
    }
  }
}
</style>
