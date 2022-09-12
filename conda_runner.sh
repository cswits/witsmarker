#!/bin/bash
eval "$(/anaconda/bin/conda shell.bash hook)"
conda activate csam2
python $@ <&0 2>&2 >&1

