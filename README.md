# EpubArchiver

## Install

via [Composer](http://getcomposer.org/)

require `"denshoch/epubarchiver": "dev-master"`.

via Github

Install Composer first, then

    git clone https://github.com/denshoch/EpubArchiver.git
    cd EpubArchiver
    composer install


## EpubArchiver class

Build a EPUB file from an input directory.

    use Denshoch\Kagyu\OCF;
    
    $arc = new EpubArchiver;
    
    $arc->build('/path/to/input_dir');
    // @return /path/to/input_dir.epub

or 

    $arc->build('/path/to/input_dir', '/another/path/to/my-awsome-book.epub');
    // @return /another/path/to/my-awsome-book.epub

if `/path/to/input_dir/META-INF/container.xml` does not exist, EpubArchiver automatically create it.

## ContainerDocument class

ContainerDocument is an EpubArchiver's component which creates `container.xml`. 

    use Denshoch\Kagyu\OCF;
    
    $doc = new ContainerDocument('/path/to/input_dir');
    // find root file(.opf or .pdf)
    $doc->scan();
    // container.xml
    $xmlString = $doc->save();

## Copyright

Copyright (c) 2013, Densho Channel  
<http://densho.hatenablog.com/>  
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

