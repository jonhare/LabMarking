#!/usr/bin/env /usr/local/bin/groovy

//take the raw unzipped dir structure from handin and create
//a new dir structure by creating symlinks to only the last
//submission for each student.

if (args.length == 0) {
	println("Usage: filterHandins.groovy handin-dir <filtered-dir>")
	System.exit(0)
}


def indir = args[0] as File
def outdir 

if (args.length>1)
	outdir = args[1] as File;
else
	outdir = new File(indir.absoluteFile.parentFile.toString(), "handin-filtered");

def files = [:]
indir.eachFile{
	if (it.isDirectory()) {
		student = it.name.substring(0, it.name.indexOf("."))
		subm = it.name.substring(it.name.indexOf(".") + 1) as Integer
		if ((!files[student]) || files[student] < subm)
			files[student] = subm
	}
}

outdir.mkdirs()

files.each{k,v ->
	def file = new File(indir.absoluteFile.toString(), k + "." + v);
	println file
	def targetLocation = new File(outdir.toString(), k);
	// ['ln', '-s', file.absolutePath, targetLocation].execute().waitFor() 
	['mv', file.absolutePath, targetLocation].execute().waitFor() 
}