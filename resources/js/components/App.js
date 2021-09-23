import React from 'react';
import ReactDOM from 'react-dom';
import {useState} from 'react';

function App() {
    const [selectedFile, setSelectedFile] = useState();
    const [isFilePicked, setIsFilePicked] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, serError] = useState(false);
    const [responseUpload, setResponse] = useState();

    const changeHandler = (event) => {
        setSelectedFile(event.target.files[0]);
        setIsFilePicked(true);
        setSubmitted(false);
    };

    const handleSubmission = () => {
        const formData = new FormData();

        setSubmitted(true);
        setResponse(null);
        formData.append('document', selectedFile);
        fetch(
            'api/file-upload',
            {
                method: 'POST',
                body: formData,
            }
        )
            .then((response) => response.json())
            .then((result) => {
                setResponse(result);
            })
            .catch((error) => {
                serError(error);
                console.error('Error:', error);
            });
    };

    return (
        <div className="container">
            <h3>Upload File</h3>
            <div>
                <input type="file" name="file" onChange={changeHandler} />
                {isFilePicked ? (
                    <div>
                        <p>Filename: {selectedFile.name}</p>
                        <p>Filetype: {selectedFile.type}</p>
                        <p>Size in bytes: {selectedFile.size}</p>
                        <p>
                            lastModifiedDate:{' '}
                            {selectedFile.lastModifiedDate.toLocaleDateString()}
                        </p>
                    </div>
                ) : null}
                <div>
                    <button onClick={handleSubmission}>Upload</button>
                </div>
                {submitted ? (<div>
                    {responseUpload ? (
                            <div>
                                {responseUpload.success ? (
                                    <p>Upload is successful</p>
                                ) : (
                                    <b className={"error"}> {responseUpload.message}</b>
                                )}
                            </div>
                        ) : null}
                    {error ? (
                        <div>
                            <p> Error {error} </p>
                        </div>
                    ) : null}
                    </div>
                ) : null}
            </div>
        </div>
    );
}

export default App;

if (document.getElementById('root')) {
    ReactDOM.render(<App />, document.getElementById('root'));
}
