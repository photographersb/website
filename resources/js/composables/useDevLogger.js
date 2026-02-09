export const useDevLogger = () => {
  const enabled = import.meta.env.DEV;
  const log = (...args) => {
    if (enabled) {
      console.log(...args);
    }
  };
  const warn = (...args) => {
    if (enabled) {
      console.warn(...args);
    }
  };
  return { log, warn };
};
