<template>
  <div class="p-2">
    <h4 class="mb-4">AI Recorder</h4>

    <b-button
      :variant="recording ? 'danger' : 'primary'"
      @click="toggleRecording"
    >
      {{ recording ? '⏹️ Stop Recording' : '▶️ Start Recording' }}
    </b-button>

    <p class="mt-3">
      <b-badge variant="info" v-if="recording">{{ formattedTime }}</b-badge>
      <b-badge variant="secondary" v-else>No recording in progress</b-badge>
    </p>

    <hr>

    <!-- <h4>Recordings</h4>
    <ul class="list-unstyled">
      <li v-for="(clip, index) in recordings" :key="index" class="mb-3">
        <div class="d-flex align-items-center justify-content-between">
          <strong>{{ clip.name }}</strong>
          <div>
            <b-button size="sm" variant="outline-secondary" :href="clip.url" download>
              💾 Download
            </b-button>
            <b-button size="sm" variant="outline-success" class="ml-2" @click="uploadClip(clip)">
              ⬆️ Upload
            </b-button>
            <b-button
              size="sm"
              variant="outline-danger"
              class="ml-2"
              @click="deleteClip(index)"
            >
              🗑️ Delete
            </b-button>
          </div>
        </div>
        <audio :src="clip.url" controls class="w-100 mt-2"></audio>
      </li>
    </ul> -->
    <!-- <h4 class="mt-4 mb-4">🎧 Your Recordings</h4> -->
    <h4 class="mt-4 mb-4">Your Recordings</h4>
    <b-row v-for="(clip, index) in recordings" :key="index" class="mb-3">
      <b-col>
        <b-card no-body class="shadow-sm p-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ clip.name }}</h5>
            <b-button-group size="sm">
              <!-- <b-button variant="outline-secondary" :href="clip.url" download>
                💾 Save
              </b-button> -->
              <b-button variant="outline-success" @click="uploadClip(clip)">
                ⬆️ Upload
              </b-button>
              <b-button variant="outline-danger" @click="deleteClip(index)">
                🗑️ Delete
              </b-button>
            </b-button-group>
          </div>
          <b-form-group class="mt-2 mb-0">
            <audio :src="clip.url" controls class="w-100" />
          </b-form-group>
        </b-card>
      </b-col>
    </b-row>

    <b-alert v-if="uploadStatus" variant="info" class="mt-4" dismissible>
      {{ uploadStatus }}
    </b-alert>
  </div>
</template>

<script>
export default {
  data() {
    return {
      mediaRecorder: null,
      audioChunks: [],
      recording: false,
      timer: null,
      recordTime: 0,
      recordings: [],
      uploadStatus: ''
    };
  },
  computed: {
    formattedTime() {
      const min = Math.floor(this.recordTime / 60).toString().padStart(2, '0');
      const sec = (this.recordTime % 60).toString().padStart(2, '0');
      return `Recording... ${min}:${sec}`;
    }
  },
  methods: {
    async toggleRecording() {
      if (!this.recording) {
        await this.startRecording();
      } else {
        this.stopRecording();
      }
    },
    async startRecording() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        this.mediaRecorder = new MediaRecorder(stream);
        this.audioChunks = [];

        this.mediaRecorder.ondataavailable = e => {
          this.audioChunks.push(e.data);
        };

        this.mediaRecorder.onstop = () => {
          const blob = new Blob(this.audioChunks, { type: 'audio/mp3' });
          const url = URL.createObjectURL(blob);
          const name = `Recording ${this.recordings.length + 1}`;

          this.recordings.unshift({ name, blob, url });
          this.recording = false;
          clearInterval(this.timer);
        };

        this.mediaRecorder.start();
        this.recording = true;
        this.recordTime = 0;

        this.timer = setInterval(() => {
          this.recordTime++;
        }, 1000);
      } catch (err) {
        console.error('Microphone access error:', err);
        this.uploadStatus = '❌ Cannot access microphone';
      }
    },
    stopRecording() {
      if (this.mediaRecorder && this.recording) {
        this.mediaRecorder.stop();
      }
    },
    async uploadClip(clip) {
      const rawData = new FormData()
      rawData.set('file', clip.blob, `${clip.name}.mp3`)
      try {
        const res = await fetch('http://192.168.0.197:7777/transcripts/upload/', {
          method: 'POST',
          body: rawData
        });

        console.log(res)
        const data = await res.json(); // 👈 Parses JSON response
        console.log(data); // Do something with the data
        this.uploadStatus = res.ok
          ? `✅ Uploaded ${clip.name} `
          : `❌ Failed to upload ${clip.name}`;
      } catch (err) {
        console.error(err);
        this.uploadStatus = `⚠️ Error uploading ${clip.name}`;
      }
    },
    deleteClip(index) {
      const confirmDelete = confirm('Are you sure you want to delete this recording?');
      if (confirmDelete) {
        // Revoke the object URL to free memory
        URL.revokeObjectURL(this.recordings[index].url);
        this.recordings.splice(index, 1);
      }
    }
  }
};
</script>

<style scoped>
audio {
  max-width: 100%;
}
</style>
