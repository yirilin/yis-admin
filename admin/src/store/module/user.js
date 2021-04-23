import { login,loginOut } from '@/api/user'
import router from '@/router/index'
export const user = {
    namespaced: true,
    state: {
        userInfo: {
            uuid: "",
            nickName: "",
            headerImg: "",
            authority: "",
        },
        token: "",
    },
    mutations: {
        setUserInfo(state, userInfo) {
            // 这里的 `state` 对象是模块的局部状态
            state.userInfo = userInfo
        },
        setToken(state, token) {
            // 这里的 `state` 对象是模块的局部状态
            state.token = token
        },
        LoginOut(state) {
            state.userInfo = {}
            state.token = ""
            router.push({ name: 'login', replace: true })
            sessionStorage.clear()
            window.location.reload()
        },
        ResetUserInfo(state, userInfo = {}) {
            state.userInfo = {
                ...state.userInfo,
                ...userInfo
            }
        }
    },
    actions: {
        async LoginIn({ commit }, loginInfo) {
            const res = await login(loginInfo).catch(res => console.log(res))
            if (res&&res.code == 200) {
                commit('setUserInfo', res.data)
                commit('setToken', res.data.token)
                const redirect = router.history.current.query.redirect
                if (redirect) {
                    router.push({ path: redirect })
                } else {
                    router.push({ path: '/admin/home' })
                    /*.catch(error => {
                        console.info(error.message)
                    })*/
                }
            }
        },
        async LoginOut({ commit }) {
            const res = await loginOut()
            if (res.code == 200) {
                commit("LoginOut")
            }
        }
    },
    getters: {
        userInfo(state) {
            return state.userInfo
        },
        token(state) {
            return state.token
        },

    }
}