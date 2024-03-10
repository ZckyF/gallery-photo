<template>
  <div class="comment-image mt-10">
    <v-expansion-panels style="max-height: 500px; overflow-y: auto; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1)" @click="handleExpansionClick" :disabled="loadingSubmit">
      <v-expansion-panel style="background-color: inherit">
        <v-expansion-panel-title>
          <template v-slot:default="{ expanded }">
            <v-row no-gutters>
              <v-col cols="4" class="d-flex justify-start comment-title">
                <v-icon icon="mdi-comment" class="mr-3" style="color: grey" />
                <h1>Comments</h1>
              </v-col>
              <v-col cols="8" class="fade-trans">
                <v-fade-transition leave-absolute>
                  <span v-if="expanded" key="0"> <h3>Share your thoughts if you find this photo captivating!</h3> </span>
                  <span v-else key="1">
                    <h3>{{ formData.comment }}</h3>
                  </span>
                </v-fade-transition>
              </v-col>
            </v-row>
          </template>
        </v-expansion-panel-title>
        <v-expansion-panel-text>
          <v-skeleton-loader v-for="index in comments.length" :key="'skeleton-' + index" v-if="skeletonLoader" type="list-item-avatar-three-line" width="400" style="background-color: transparent"></v-skeleton-loader>

          <div v-else v-for="(comment, index) in comments" :key="'comment-' + index">
            <div class="user-comments mb-8 mt-3" v-if="comment.parent_id === null">
              <NuxtLink to="/" class="image-profile-user-comment">
                <img :src="`${urlAvatar}${comment.user.avatar_name}`" alt="" />
              </NuxtLink>
              <div class="user-info">
                <NuxtLink to="/">{{ comment.user.username }}</NuxtLink>
                <span class="created-at ml-3">{{ convertDate(comment.user.created_at) }}</span>
                <p>{{ comment.comment }}</p>

                <div class="reply-buttons mt-1">
                  <div
                    class="reply-button"
                    @click="
                      textUsername = comment.user.username;
                      chipUsername = true;
                      formData.parent_id = comment.id;
                    "
                  >
                    Reply
                  </div>
                  <div class="reply-button" @click="showReplies = !showReplies" v-if="comment.replies && comment.replies.length > 0">{{ !showReplies ? 'See Replies' : 'Close Replies' }}</div>
                  <div
                    class="reply-button"
                    v-if="comment.user.id == store.getUser.id"
                    style="color: #d32f2f"
                    @click="
                      dialogDelete = true;
                      idComment = comment.id;
                    "
                  >
                    Delete
                  </div>
                </div>
                <div v-if="showReplies">
                  <div v-for="(reply, replyIndex) in comment.replies" :key="replyIndex" class="user-comments mb-8 mt-3">
                    <NuxtLink to="/" class="image-profile-user-comment">
                      <img :src="`${urlAvatar}${reply.user.avatar_name}`" alt="" />
                    </NuxtLink>
                    <div class="user-info">
                      <NuxtLink to="/">{{ reply.user.username }}</NuxtLink>
                      <span class="created-at ml-3">{{ convertDate(reply.user.created_at) }}</span>
                      <p>{{ reply.comment }}</p>
                      <div class="reply-buttons mt-1">
                        <!-- <div
                          class="reply-button"
                          @click="
                            textUsername = reply.user.username;
                            chipUsername = true;
                          "
                        >
                          Reply
                        </div> -->
                        <div
                          class="reply-button"
                          v-if="comment.user.id == store.getUser.id"
                          style="color: #d32f2f"
                          @click="
                            dialogDelete = true;
                            idComment = reply.id;
                          "
                        >
                          Delete
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="input-comments mt-7">
            <v-form>
              <v-row>
                <v-col cols="12" md="11">
                  <v-text-field prepend-icon="mdi-comment-text" variant="underlined" v-model="formData.comment" hide-details placeholder="Add Comment">
                    <v-chip
                      closable
                      v-if="chipUsername == true"
                      @click:close="
                        chipUsername = false;
                        formData.parent_id = '';
                      "
                    >
                      {{ textUsername }}
                    </v-chip>
                  </v-text-field>
                </v-col>
                <v-col cols="12" md="1">
                  <v-btn class="send-comment mt-2 text-none" variant="outlined" prepend-icon="mdi-send" @click="handleSubmit" :loading="loadingSubmit">Send</v-btn>
                </v-col>
              </v-row>
            </v-form>
          </div>
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
    <v-dialog v-model="dialogDelete" transition="dialog-top-transition" max-width="400">
      <v-card>
        <v-card-text class="pa-8 card-text-dialog">
          <span class="mx-auto icon-alert">
            <v-icon size="60">mdi-delete</v-icon>
          </span>

          <div class="text-center text-delete mt-5">
            <h3>Do you want to delete this comment ?</h3>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn variant="text" class="text-none" @click="deleteSubmit" :loading="loadingDelete">Yes</v-btn>
          <v-btn
            variant="text"
            class="text-none"
            @click="
              dialogDelete = false;
              idComment = '';
            "
            :disabled="loadingDelete"
            >Close</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
    <AlertsSnackbar :snackbar="snackbar" :snackMes="snackMes" @update:snackbar="handleSnackbarClose" />
  </div>
</template>

<script lang="ts" setup>
import axios from 'axios';
import { ref } from 'vue';
import { convertDate } from '~/helpers/convert.ts';
import { useUserStore } from '~/stores/store';

const store = useUserStore();

const props = defineProps(['idPhoto']);

let comments = ref<[]>([]);
let formData = ref<object>({
  photo_id: '',
  comment: '',
  parent_id: '',
});
let idComment = ref<string>('');

const url = useRuntimeConfig().public.URL_API;
const urlAvatar = useRuntimeConfig().public.URL_AVATAR;
const cookie = useCookie('auth_token');

let chipUsername = ref<boolean>(false);
let textUsername = ref<string>('');
let showReplies = ref<boolean>();
let skeletonLoader = ref<boolean>(false);
let dataFetched = ref<boolean>(false);
let loadingSubmit = ref<boolean>(false);
let dialogDelete = ref<boolean>(false);
let loadingDelete = ref<boolean>(false);
let snackbar = ref<boolean>(false);
let snackMes = ref<object>({ text: '', prependIcon: '' });

const showSnackbar = (message: string, type: string = 'success') => {
  snackMes.value = { text: message, prependIcon: type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' };
  snackbar.value = true;
  // $ref.snackbar.value.show(snackMes);
  setTimeout(() => {
    snackbar.value = false;
  }, 5000);
};
const getDataComments = async () => {
  try {
    skeletonLoader.value = true;
    const { data: commentPhotos } = await useFetch(`${url}/photo/${props.idPhoto}/comments`);
    comments.value = commentPhotos.value.data;
    console.log(comments.value);
  } catch (error) {
    console.log(error);
  } finally {
    setTimeout(() => {
      skeletonLoader.value = false;
    }, 500);
  }
};

const handleExpansionClick = () => {
  if (!dataFetched.value) {
    dataFetched.value = true;
    getDataComments();
  }
};

const handleSubmit = async () => {
  try {
    loadingSubmit.value = true;
    formData.value.photo_id = props.idPhoto;
    console.log(formData.value);

    // Kirim permintaan untuk menambahkan like
    const response = await axios.post(`${url}/comment-photos`, formData.value, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });

    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response.data.message);
    chipUsername.value = false;

    formData.value = { photo_id: '', comment: '', parent_id: '' };
    // Perbarui data likes setelah like berhasil ditambahkan
    const { data: updatedComment } = await useFetch(`${url}/photo/${props.idPhoto}/comments`);
    comments.value = updatedComment.value.data;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding comment:', error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loadingSubmit.value = false;
  }
};
const deleteSubmit = async () => {
  try {
    loadingDelete.value = true;
    formData.value.photo_id = props.idPhoto;
    console.log(idComment.value);

    // Kirim permintaan untuk menambahkan like
    const response = await axios.delete(`${url}/comment-photos/${idComment.value}`, {
      headers: {
        Authorization: `Bearer ${cookie.value}`,
      },
    });

    // Tampilkan pesan sukses atau lakukan tindakan lain yang diperlukan
    console.log(response.data.message);
    // Perbarui data likes setelah like berhasil ditambahkan
    const { data: updatedComment } = await useFetch(`${url}/photo/${props.idPhoto}/comments`);
    comments.value = updatedComment.value.data;
  } catch (error) {
    // Tampilkan pesan kesalahan atau lakukan tindakan lain yang diperlukan
    console.error('Error adding like:', error);
    showSnackbar(error.response.data.message, 'error');
  } finally {
    loadingDelete.value = false;
    dialogDelete.value = false;
  }
};
const handleSnackbarClose = () => {
  snackbar.value = false;
};
</script>

<style lang="scss" scoped>
@use '~/assets/scss/abstracts/variables';
@use '~/assets/scss/abstracts/mixins';
@use '~/assets/scss/components/buttons';
.comment-image {
  .v-expansion-panel__shadow {
    box-shadow: none !important;
  }
  .fade-trans {
    h3 {
      color: variables.$secondary-black;
      font-weight: 500;
      font-size: 15px;
    }
  }
  .comment-title {
    h1 {
      color: variables.$primary-black;
      font-weight: 500;
      font-size: 18px;
    }
  }
  .user-comments {
    display: flex;
    align-items: flex-start;
    gap: 15px;

    .image-profile-user-comment {
      img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: all 0.2s ease;

        &:hover {
          filter: brightness(70%);
        }
      }
    }

    .user-info {
      a {
        text-decoration: none;
        color: variables.$primary-black;
        font-weight: 700;

        &:hover {
          color: variables.$secondary-black;
        }
      }
      span {
        font-size: 13px;
      }
      p {
        margin: 0;
        color: variables.$secondary-black;
        font-size: 15px;
      }

      .reply-buttons {
        display: flex;
        gap: 30px;
      }
      .reply-button {
        text-align: start;
        color: variables.$secondary-black;
        font-size: 14px;
        cursor: pointer;
        &:hover {
          color: variables.$primary-black;
        }
      }
    }
  }
  .input-comments {
    position: sticky;
    bottom: 0;
    background-color: variables.$primary-background-color;
    .send-comment {
      letter-spacing: normal;
      border-color: variables.$border-color-outlined;
      &:hover {
        border-color: variables.$hover-border-color-outlined;
      }
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
