import axios from 'axios';
import index from '../router/index';
import qs from 'qs';

axios.defaults.timeout = 8000;
// axios.defaults.baseURL = 'http://www.shenfupay.net/api/';
axios.defaults.baseURL = 'http://www.sfpay.com/api/';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded,multipart/form-data';

// http request 拦截器
axios.interceptors.request.use(
  config => {
    config.data = qs.stringify(config.data);
    let token =JSON.parse(localStorage.getItem('user'));
    if (token && token.data.access_token !== 'undefined') { //判断token是否存在
      config.headers.Authorization = 'Bearer ' + token.data.access_token;  //将token设置成请求头
    }
    console.log(config)
    return config;
  },
  err => {
    return Promise.reject(err);
  }
);

// http response 拦截器
axios.interceptors.response.use(
  response => {
    console.log(response)
    if (response.data.code === -1) {
      localStorage.removeItem('user')
      location.reload()
    }
    return response;
  },
  error => {
    return Promise.reject(error);
  }
)

const ajax = option => {
  let data = {
    url: option.url, //接口
    method: option.method || "get",
    // params: option.data || {},
    // errorCallBack:option.errorCallBack
  }
  if(option.method=='post' || option.method=="POST"){
    data.data = option.data || {}
  }else{
    data.params = option.data || {}
  }
  axios(data)
    .then(r => {
      //成功回调
      typeof option.then === 'function' && option.then(r.data)
    })
    // .catch(r => {
    //   console.log('失败：' + r)
    // })
}

export {
  ajax
}

