<template>
    <div class="modern-label" :class="variant">
        <div class="wrapper">
            <label>{{ label }}</label>
            <small v-if="small"> {{ small }}</small>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ModernLabel',
    props: {
        label: {
            type: String,
            default: 'Initial Title'
        },
        small: {
            type: String,
            default: ''
        },
        variant: {
            type: String,
            default: 'primary'
        }
    }
}
</script>
<style scoped>
.modern-label{
    background-color: #ECF2F5;
    position: relative;
    border-radius: 4px;
}

.modern-label.primary:before{ 
    border-left: 8px solid #648DE1;
}

.modern-label.ax-primary-400:before{ 
    border-left: 8px solid #0D98BA;
}

.modern-label.w3green:before{ 
    border-left: 8px solid #03a972;
}

.modern-label.orange-500:before{ 
    border-left: 8px solid #FA6E15;
}

.modern-label.brown:before{ 
    border-left: 8px solid #a58e7c;
}

.modern-label:before{
    content: "";
    height: 100%;
    position: absolute;
}

.modern-label .wrapper{
    padding: 12px 24px;
}

.modern-label small{
    font-size: 12px;
    line-height: 100%;
    font-weight: 400;
    margin-bottom: 0;
    color:#6C757D;
    font-style: italic;
    display: inline-block;
}

.modern-label label{
    font-size: 14px;
    line-height: 100%;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0;
    color:#1B1B1B;
    display: block;
}

@media screen and (max-width: 768px) {
    .modern-label .wrapper{
        padding: 12px 16px;
    }
    .modern-label label{
        font-size: 12px;
    }
    .modern-label small{
        font-size: 10px;
    }
}
</style>