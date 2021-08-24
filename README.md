# Metadata Manager

This application is a CLI wrapper around the PHP getId3 library that allows you to input a file and generate a JSON file containing the important metadata about that media file, or conversely to read a JSON file and write the corresponding metadata to the specified media file.

## Usage

#### Read Metadata to JSON File (and artwork image)
```
metadata-manager read file-path json-output-path [art-output-path]
```

#### Write Metadata from JSON File (and artwork image)
```
metadata-manager write file-path json-input-path [art-input-path]
```
