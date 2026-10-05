<template>
    <div>
        <!-- Suggestion Bar -->
        <b-row class="mt-2 mb-2 no-gutters flex-nowrap overflow-auto">
            <b-button
                v-for="(suggestion, index) in suggestions"
                :key="index"
                variant="primary"
                size="sm"
                class="mr-2 rounded-pill flex-shrink-0 text-truncate"
                style="max-width: 150px;"
                @click="selectSuggestion(suggestion)"
            >
            {{ suggestion.education_while_at_home }}
            </b-button>
        </b-row>

        <!-- Chat Input Area -->
        <!-- <div class="d-flex align-items-center p-2 border rounded-pill bg-white">

            <b-button variant="link" class="p-0 mr-2">
                😊
            </b-button>

            <input
                type="text"
                class="form-control border-0"
                v-model="chatMessage"
                placeholder="Tulis Pesan..."
                style="flex: 1;"
            />

            <b-button variant="link" class="p-0 mx-2">
                ➕
            </b-button>

            <b-button
                variant="success"
                class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px;"
                @click="sendMessage"
            >
                <b-icon icon="send-fill" font-scale="1.2"></b-icon>
            </b-button>
        </div> -->
    </div>
</template>

<script>
export default {
    props: {
        items: {
            type: Array,
        }
    },
    watch: {
        // Watch for props change and update local copy
        items: {
            immediate: true,
            handler(newItems) {
                this.suggestions = [...newItems]; // Clone the array
            }
        }
    },
    data() {
        return {
            suggestions: [],
            // chatMessage: ""
        };
    },
    methods: {
        selectSuggestion(text) {
            // this.chatMessage = text;
            this.$emit('fillEducationResume', text)
        },
        // sendMessage() {
        //     if (this.chatMessage.trim()) {
        //         console.log("Sending:", this.chatMessage);
        //         this.chatMessage = "";
        //     }
        // }
    }
};
</script>

<style scoped>
    /* Smooth scroll for mobile */
    .b-row {
        -webkit-overflow-scrolling: touch;
    }

    /* Hide button outlines on click */
    .b-button:focus {
        box-shadow: none;
    }

    /* Truncate long suggestion text */
    .text-truncate {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
