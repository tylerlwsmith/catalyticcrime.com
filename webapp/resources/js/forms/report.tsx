// @ts-nocheck
import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom";
import axios from "axios";

const reportFormElement = document.getElementById("report-form");
if (reportFormElement) {
    ReactDOM.render(<Form />, document.getElementById("report-form"));
}

function Form() {
    const [streetAddress1, setStreetAddress1] = useState("");
    const [streetAddress2, setStreetAddress2] = useState("");
    const [zip, setZip] = useState("");

    function preventInvalidZipInput(event: React.KeyboardEvent) {
        // @ts-ignore
        const currentValue = event.target.value;
        if (currentValue.length >= 5 || !/\d/.test(event.key)) {
            event.preventDefault();
            return false;
        }
    }

    return (
        <form action="" onSubmit={() => {}}>
            <label htmlFor="street-address-1">Street Address</label>
            <div>
                <input
                    id="street-address"
                    type="text"
                    value={streetAddress1}
                    onChange={(event) => setStreetAddress1(event.target.value)}
                />
            </div>

            <label htmlFor="street-address-2">Street Address 2</label>
            <div>
                <input
                    id="street-address-2"
                    type="text"
                    value={streetAddress2}
                    onChange={(event) => setStreetAddress2(event.target.value)}
                />
            </div>

            <label htmlFor="city">City</label>
            <div>
                <input
                    id="city"
                    type="text"
                    value={"Bakersfield"}
                    onChange={(event) => null}
                    readOnly={true}
                />
            </div>

            <label htmlFor="state">State</label>
            <div>
                <input
                    id="state"
                    type="text"
                    value={"CA"}
                    onChange={(event) => null}
                    readOnly={true}
                />
            </div>

            <label htmlFor="zip">ZIP</label>
            <div>
                <input
                    id="zip"
                    type="text"
                    required={true}
                    pattern="[0-9]{4,5}"
                    inputMode="numeric"
                    onKeyPress={preventInvalidZipInput}
                    onChange={(event) => setZip(event.target.value)}
                    value={zip}
                />
            </div>

            <label htmlFor="make">Vehicle type</label>
            <div>
                <CarSelector />
            </div>

            <label htmlFor="police-report-number">
                Police report number (optional)
            </label>

            <div>
                <input id="police-report-number" type="text" onChange={} />
            </div>

            <div>Uploads</div>

            <div>Description (optional)</div>
        </form>
    );
}

function CarSelector() {
    const [makes, setMakes] = useState([]);
    const [models, setModels] = useState([]);
    const [years, setYears] = useState([]);

    const [make, setMake] = useState("");
    const [model, setModel] = useState("");
    const [year, setYear] = useState("");

    useEffect(function queryMakes() {
        axios
            .get(`/vehicles`)
            .then(({ data }) => setMakes(data))
            .catch((error) => setMakes([data]));
    }, []);

    useEffect(
        function queryModels() {
            if (make === "") {
                setModel("");
                setModels([]);
                return;
            }
            axios
                .get(`/vehicles/${doubleEncodeUri(make)}`)
                .then(({ data }) => setModels(data))
                .catch((error) => setModels([]));
        },
        [make, makes]
    );

    useEffect(
        function queryYears() {
            console.log("running year effect.");
            if (model === "") {
                setYear("");
                setYears([]);
                return;
            }
            axios
                .get(
                    `/vehicles/${doubleEncodeUri(make)}/${doubleEncodeUri(
                        model
                    )}`
                )
                .then(({ data }) => setYears(data))
                .catch((error) => setYears([]));
        },
        [model, models]
    );

    function doubleEncodeUri(string: string) {
        return encodeURIComponent(encodeURIComponent(string));
    }

    console.log({ makes, models, years, make, model, year });

    return (
        <div>
            <select
                id="make"
                value={make}
                onChange={(event) => setMake(event.target.value)}
            >
                <option value="" children="Select Make" />
                {makes.map((make) => (
                    <option
                        key={make.make}
                        value={make.make}
                        children={make.make}
                    />
                ))}
            </select>

            <select
                id="model"
                value={model}
                onChange={(event) => setModel(event.target.value)}
                className={models.length === 0 ? "opacity-25" : ""}
                disabled={models.length === 0}
            >
                <option value="" children="Select Model" />
                {models.map((model) => (
                    <option
                        key={model.model}
                        value={model.model}
                        children={model.model}
                    />
                ))}
            </select>

            <select
                id="year"
                value={year}
                onChange={(event) => setYear(event.target.value)}
                className={years.length === 0 ? "opacity-25" : ""}
                disabled={years.length === 0}
            >
                <option value="" children="Select Year" />
                {years.map((year) => (
                    <option
                        key={year.year}
                        value={year.year}
                        children={year.year}
                    />
                ))}
            </select>
        </div>
    );
}
