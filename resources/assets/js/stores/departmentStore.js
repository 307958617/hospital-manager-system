/**
 * Created by Administrator on 2018/3/23.
 */
const state={       //state就是存放变量的仓库
    authUser:null
};
const mutations= {   //它是负责操作上面state里面的数据的
    get_auth_user(state,user) {
        state.authUser = user
    }
};
const actions= {   //它是负责从后台提取数据的
    getAuthUser(store,user) {
        store.commit('get_auth_user',user)
    }
};

export default {
    state,mutations,actions
}
