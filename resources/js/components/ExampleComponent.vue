<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- Modal -->
        <div
          class="modal fade"
          id="exampleModal"
          tabindex="-1"
          role="dialog"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-email" id="exampleModalLabel">
                  <b>Add User</b>
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="d-flex justify-content-center">
                  <form @submit.prevent="addUser" class="mb-3">
                    <div class="form-group">
                      <input
                        required
                        type="text"
                        class="form-control"
                        placeholder="Nombre"
                        v-model="user.email"
                      />
                    </div>
                    <div class="form-group">
                      <input
                        required
                        type="email"
                        class="form-control"
                        placeholder="email"
                        v-model="user.name"
                      />
                    </div>
                    <button
                      type="submit"
                      data-dismiss="modal"
                      @click="addUser()"
                      class="btn btn-info"
                    >
                      Save
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-center" v-if="loading">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <div v-else>
          <nav aria-label="Page navigation example ">
            <ul class="pagination pt-5">
              <button
                type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#exampleModal"
              >
                Add User +
              </button>
            </ul>
          </nav>

          <div
            class="alert alert-dismissible fade show"
            :class="{
              'btn-danger': message.danger,
              'btn-success': message.success,
            }"
            v-if="message.status"
            role="alert"
          >
            <strong>Holy Dear!</strong>
            {{ message.content }}
            <button
              type="button"
              class="close"
              data-dismiss="alert"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="card">
            <div class="card-body" v-for="user in users" v-bind:key="user.id">
              <h3>
                <b>{{ user.email }}</b>
              </h3>
              <p>{{ user.name }}</p>
              <button
                @click="editUser(user)"
                type="button"
                class="btn btn-warning"
                data-toggle="modal"
                data-target="#exampleModal"
              >
                Edit
              </button>

              <button
                @click="deleteUser(user.id)"
                type="button"
                class="btn btn-danger"
              >
                Delete
              </button>
            </div>
          </div>
          <br />
          <br />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      user: {
        id: "",
        email: "",
        body: "",
      },
      user_id: "",
      pagination: {},
      edit: false,
      loading: true,
      message: {
        status: false,
        content: "",
        danger: false,
        success: true,
      },
    };
  },
  created() {
    this.fetchUsers();
  },
  methods: {
    fetchUsers(page_url) {
      let vm = this;
      this.loading = true;
      page_url = page_url || "api/users/";
      fetch(page_url)
        .then((res) => res.json())
        .then((res) => {
          console.log(res.data);

          this.loading = false;
          this.users = res.data;
          this.hideAlert();
          vm.makePagination(res.meta, res.links);
        })
        .catch((err) => {
          
        });
    },
    makePagination(meta, links) {
      let pagination = {
        current_page: meta.current_page,
        last_page: meta.last_page,
        next_page_url: links.next,
        prev_page_url: links.prev,
      };
      this.pagination = pagination;
    },
    deleteUser(id) {
      if (confirm("Are you sure?")) {
        this.loading = true;
        fetch(`api/user/${id}`, {
          method: "delete",
        })
          .then((res) => res.json)
          .then((data) => {
            this.message.content = "User has been removed";
            this.showMessage("User has been removed.", true, false, true);
            this.fetchUsers();
          })
          .catch((err) => {
            console.log(err);
            this.showMessage(
              "An error occured, please try again later.",
              false,
              true,
              true
            );
            this.loading = false;
            this.hideAlert();
          });
      }
    },
    hideAlert() {
      setTimeout((res) => (this.message.status = false), 9000);
    },
    showMessage(content, success, danger, status) {
      this.message.content = content;
      this.message.success = success;
      this.message.danger = danger;
      this.message.status = status;
    },
    performCheck() {
      if (this.user.email == "" || this.user.name == "") {
        this.showMessage("All fields are required.", false, true, true);
        return false;
      } else {
        return true;
      }
    },
    addUser() {
      if (!this.performCheck()) {
        return;
      }
      this.loading = true;
      if (this.edit === false) {
        //Add
        fetch("api/user", {
          method: "post",
          body: JSON.stringify(this.user),
          headers: {
            "content-type": "application/json",
          },
        })
          .then((res) => res.json())
          .then((data) => {

            console.log(data);
            
            this.user.email = "";
            this.user.name = "";
            this.showMessage("User has been added.", true, false, true);
            this.fetchUsers();
          })
          .catch((err) => {
            console.log(err);
            this.showMessage(
              "An error occured, please try again later.",
              false,
              true,
              true
            );
            this.loading = false;
            this.hideAlert();
          });
      } else {
        //Edit
        fetch("api/user", {
          method: "put",
          body: JSON.stringify(this.user),
          headers: {
            "content-type": "application/json",
          },
        })
          .then((res) => res.json())
          .then((data) => {
            this.user.email = "";
            this.user.name = "";
            this.showMessage("User has been updated.", true, false, true);

            this.fetchUsers();
          })
          .catch((err) => {
            console.log(err);
            this.showMessage(
              "An error occured, please try again later.",
              false,
              true,
              true
            );
            this.loading = false;
            this.hideAlert();
          });
      }
    },
    editUser(user) {
      this.edit = true;
      this.user.user_id = user.id;
      this.user.email = user.email;
      this.user.name = user.name;
    },
  },
  mounted() {},
};
</script>