#!/bin/bash
eval "$(/anaconda/bin/conda shell.bash hook)"
python $@ <&0 2>&2 >&1

