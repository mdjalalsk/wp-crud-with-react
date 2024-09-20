import { createRoot } from 'react-dom/client';
import App from "./App";

const root = createRoot(document.getElementById('react-app'));
console.log(root);
root.render(<App/>);