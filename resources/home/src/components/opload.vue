<template>
  <i-form >
    <Upload
        action="http://www.shenfupay.net/api/daoru/daoru"
        :on-error="errors"
        :before-upload="bUpload"
        type="select"
        :on-success="sucess"
        :default-file-list='list'
        :data='psw'
        name='excel'
        ref="upload"
        :headers="headers"
        :accept="'xlsx'||'xls'"
      >
        <i-button type="ghost" size="large" accept="xlsx" style="color:green">导入EXCEL文件</i-button>
      </Upload>
    <Form-item label="交易密码">
      <i-input type="password" v-model="psw.password" placeholder="请输入交易密码"></i-input>
    </Form-item>
    <Form-item>
      <i-button type="success" size="large" @click="search">导入</i-button>
      <i-button type="default" size="large" @click="reset">取消</i-button>
    </Form-item>
  </i-form>
</template>
<script>

export default {
  data(){
    return{
      psw:{
        password:''
      },
      list:[],
      headers:{
        Authorization: 'Home ' + JSON.parse(localStorage.getItem('userInfo')).data.access_token
      }
    }
  },
  methods:{
    bUpload(e){
      this.list[0]=e
      return false
    },
    errors(e){
      this.$refs.upload.clearFiles()
      this.list =[]
      this.$Message.info(e)
    },
    sucess(response,e,d){
      this.$refs.upload.clearFiles()
      this.list =[]
      if(response.code !==1){
        this.$Message.error(response.msg)
        return false
      }
      this.$emit('success')
    },
    search(){
      if(this.list.length==0){
        this.$Message.error('请选择文件')
        return false
      }
      if(!this.psw.password){
        this.$Message.error('请输入交易密码')
        return false
      }
      this.$refs.upload.post(this.list[0]);
    },
    reset(){
      this.$emit('errors')
    }
  }
}
</script>
<style scoped>

</style>

