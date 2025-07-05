import { onMounted, onUnmounted } from 'vue';

export default function useInfiniteScroll(callback, { threshold = 200 } = {}) {
  if (typeof callback !== 'function') {
    throw new Error('useInfiniteScroll requires a callback function');
  }

  const handleScroll = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight - threshold) {
      callback();
    }
  };

  const start = () => {
    window.addEventListener('scroll', handleScroll);
  };

  const stop = () => {
    window.removeEventListener('scroll', handleScroll);
  };

  // auto start by default
  onMounted(start);
  onUnmounted(stop);

  return { start, stop };
} 