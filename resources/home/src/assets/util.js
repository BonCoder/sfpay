import axios from 'axios';
import index from '../router/index';
import qs from 'qs';
import iview from 'iview';
import 'iview/dist/styles/iview.css';

axios.defaults.timeout = 8000;
axios.defaults.baseURL = 'http://www.shenfupay.net/api/';
// axios.defaults.baseURL = 'http://www.sfpay.com/api/';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded';

// http request 拦截器
axios.interceptors.request.use(
  config => {
    config.data = qs.stringify(config.data);
    let token =JSON.parse(localStorage.getItem('userInfo'));
    if (token) { //判断token是否存在
      config.headers.Authorization = 'Home ' + token.data.access_token;  //将token设置成请求头
    }
    return config;
  },
  err => {
    return Promise.reject(err);
  }
);

// http response 拦截器
axios.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    let _response = error.response;
    switch (_response.status) {
      case 401:
        // 401 未授权，请重新登录
        return index.replace({
          //跳转到登录页面
          path: "/login",
          name: '登录',
          component: '@/components/login'
        });
      case 500:
        return Promise.reject("服务器出错：", error.response.data);
    }
    return Promise.reject(error.response.data); // 返回接口返回的错误信息
  }
)

const ajax = option => {
  let data = {
    url: option.url, //接口
    method: option.method || "get",
    // params: option.data || {},
    // errorCallBack:option.errorCallBack
  }
  if(option.method =='post' || option.method =="POST"){
    data.data = option.data || {}
  }else{
    data.params = option.data || {}
  }
  axios(data)
    .then(r => {
      //成功回调
      if (r.data.data.length === 0) {
        iview.Message.info('没有更多数据')
        return false
      }
      typeof option.then === 'function' && option.then(r.data)
    })
    .catch(r => {
      console.log('失败：' + r)
    })
}

export {
  ajax
}

