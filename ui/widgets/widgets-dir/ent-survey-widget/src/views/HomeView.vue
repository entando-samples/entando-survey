<script setup>
import {getData} from "../integration/integration";
</script>
<script>
export default {
  data() {
    return {
      payload: null
    }
  },
  methods:{
    callTheApi: async function callTheApi() {
      const responseObj = await getData();
      if (responseObj["response"]) {
        this.payload = responseObj.response.data.payload
      }else{
        this.payload = responseObj.error.message
      }
    }
  }
}
</script>

<template>
  <div>
    <button @click="callTheApi()">call the api</button>
  </div>
  <div>
    <span>{{payload}}</span>
  </div>
</template>

