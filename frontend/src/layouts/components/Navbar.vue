<template>
  <div class="navbar-container d-flex content align-items-center">

    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link
          class="nav-link"
          @click="toggleVerticalMenuActive"
        >
          <feather-icon
            icon="MenuIcon"
            size="21"
          />
        </b-link>
      </li>
    </ul>

    <!-- Left Col -->
    <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex">
      <dark-Toggler class="d-none d-lg-block" />
    </div>

    <b-navbar-nav class="nav align-items-center ml-auto">
      <b-nav-item-dropdown
        right
        toggle-class="d-flex align-items-center dropdown-user-link"
        class="dropdown-user"
      >
        <template #button-content>
          <div class="d-sm-flex d-none user-nav">
            <p class="user-name font-weight-bolder mb-0">
              {{UserData.username}}
            </p>
            <span class="user-status">{{UserData.role}}</span>
          </div>
          <b-avatar
            size="40"
            variant="light-primary"
            badge
            :src="require('@/assets/images/avatars/DefaultUserIcon.png')"
            class="badge-minimal"
            badge-variant="success"
          />
        </template>

        <b-dropdown-item link-class="d-flex align-items-center" v-b-modal.modal-navbar-about>
          <feather-icon
            size="16"
            icon="HelpCircleIcon"
            class="mr-50"
          />
          <span>About</span>
        </b-dropdown-item>

        <b-dropdown-item link-class="d-flex align-items-center" @click="profile">
          <feather-icon
            size="16"
            icon="UserIcon"
            class="mr-50"
          />
          <span>Profile</span>
        </b-dropdown-item>

        <b-dropdown-divider />

        <b-dropdown-item link-class="d-flex align-items-center" @click="logout">
          <feather-icon
            size="16"
            icon="LogOutIcon"
            class="mr-50"
          />
          <span>Logout</span>
        </b-dropdown-item>
      </b-nav-item-dropdown>
    </b-navbar-nav>

    <!-- About modal -->
    <modal-navbar-about />
    
  </div>
</template>

<script>
import {
  VBModal, BLink, BNavbarNav, BNavItemDropdown, BDropdownItem, BDropdownDivider, BAvatar,
} from 'bootstrap-vue'
import DarkToggler from '@core/layouts/components/app-navbar/components/DarkToggler.vue'
import ModalNavbarAbout from './ModalNavbarAbout.vue'
import { PCM } from '@/mixins/PCM.js'

const UserData = {}

export default {
  mixins: [PCM],
  components: {
    BLink,
    BNavbarNav,
    BNavItemDropdown,
    BDropdownItem,
    BDropdownDivider,
    BAvatar,

    // Navbar Components
    DarkToggler,

    ModalNavbarAbout,
  },
  directives: {
    'b-modal': VBModal,
  },
  data() {
    return {
      UserData,
    }
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },
  methods: {
    logout() {
	    this.$http.get('/api/auth/logout').then(
        localStorage.clear(),
        this.$router.push({name: 'login'})
      ).catch(error => {
        vm.DisplayError(error)
      })
    },
    profile() {
      this.$router.push({name: 'profile'})
    },
  },
  mounted() {

    this.UserData = JSON.parse(localStorage.getItem('userData'))
  }
}
</script>
