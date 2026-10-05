import { ref } from 'vue'
export function useVueSpeech(){
    // Initialize SpeechRecognition API
    const recognition = new webkitSpeechRecognition();
    recognition.continuous = false;
    recognition.lang = "en-US";
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;
    
    // Setup Vue refs
    const listening = ref(false);
    const transcript = ref("");
    const confidence = ref(0);

    // Create method to trigger the API
    const listen = () => {
        listening.value = true;
        recognition.start();
    }

    // Handle API result
    recognition.onresult = (event) => {
        listening.value = false;
        const result = event.results[0][0];
        transcript.value = result.transcript;
        confidence.value = result.confidence;
    }

    return {
        listening,
        listen,
        transcript,
        confidence
    }
};