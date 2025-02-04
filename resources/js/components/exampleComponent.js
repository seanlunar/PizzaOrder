import React from 'react';
import ReactDOM from 'react-dom';

function ExampleComponent() {
    return (
        <div>
            <h1>Hello, React!</h1>
        </div>
    );
}

export default ExampleComponent;

if (document.getElementById('example')) {
    ReactDOM.render(<ExampleComponent />, document.getElementById('example'));
}
