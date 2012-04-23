<?xml version="1.0"?>
<!--
 * Copyright (c) 2010 Fabien CALLUAUD  (smurf1.free.fr, tm-ladder.com)
 * Licensed under the MIT license.
 * http://smurf1.free.fr/sdz/licence.txt
 -->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
<!--<xsl:output indent="yes" encoding="UTF-8" method="html" doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"/>-->
<xsl:output indent="yes" encoding="UTF-8" method="xml" omit-xml-declaration="yes" />

<!-- TRAITEMENT DES LISTE À PUCES -->
<xsl:template match="liste">
	<xsl:element name="ul">
		<xsl:choose>
			<xsl:when test="@type">
				<xsl:attribute name="class">liste_<xsl:value-of select="@type" /></xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="class">liste_defaut</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<xsl:template match="puce">
	<li>
		<xsl:apply-templates />
	</li>
</xsl:template>

<!-- TRAITEMENT DES REMARQUES -->
<xsl:template match="information"><div class="rmq information"><xsl:apply-templates /></div></xsl:template>
<xsl:template match="attention"><div class="rmq attention"><xsl:apply-templates /></div></xsl:template>
<xsl:template match="erreur"><div class="rmq erreur"><xsl:apply-templates /></div></xsl:template>
<xsl:template match="question"><div class="rmq question"><xsl:apply-templates /></div></xsl:template>


<!-- MISE EN FORME SIMPLE DU TEXTE -->
<xsl:template match="gras"><span class="gras"><xsl:apply-templates /></span></xsl:template>
<xsl:template match="italique"><span class="italique"><xsl:apply-templates /></span></xsl:template>
<xsl:template match="souligne"><span class="souligne"><xsl:apply-templates /></span></xsl:template>
<xsl:template match="barre"><span class="barre"><xsl:apply-templates /></span></xsl:template>

<!-- TRAITEMENT DES TAILLES -->
<xsl:template match="taille">
	<xsl:element name="span">
		<xsl:attribute name="class"><xsl:value-of select="@valeur" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>



<!-- TRAITEMENT DES FLOTTANTS -->
<xsl:template match="flottant">
	<xsl:element name="div">
		<xsl:if test="@valeur">
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@valeur='gauche'">flot_gauche</xsl:when>
					<xsl:when test="@valeur='droite'">flot_droite</xsl:when>
					<xsl:when test="@valeur='aucun'">clearer</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES POSITIONS -->
<xsl:template match="position">
	<xsl:element name="div">
		<xsl:if test="@valeur">
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@valeur='gauche'">gauche</xsl:when>
					<xsl:when test="@valeur='droite'">droite</xsl:when>
					<xsl:when test="@valeur='centre'">centre</xsl:when>
					<xsl:when test="@valeur='justifie'">justifie</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES ZONES DE CODE -->
<xsl:template match="code_contener">
	<xsl:choose>
		<xsl:when test="@url">
			<xsl:element name="a">
				<xsl:attribute name="href"><xsl:value-of select="@url" /></xsl:attribute>
				<span class="code">Code : <xsl:value-of select="@type" />
					<xsl:if test="@titre"> - <xsl:value-of select="@titre" /></xsl:if>
			</span>
			</xsl:element>
		</xsl:when>
		<xsl:otherwise>
			<span class="code">Code : <xsl:value-of select="@type" />
				<xsl:if test="@titre"> - <xsl:value-of select="@titre" /></xsl:if>
			</span>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:element name="div">
		<xsl:attribute name="class">code2 .<xsl:value-of select="@type" /></xsl:attribute>
		<xsl:variable name="code_content"><xsl:value-of select="."/></xsl:variable>
		<xsl:variable name="code_type"><xsl:value-of select="@type"/></xsl:variable>
		<xsl:variable name="code_debut"><xsl:value-of select="@debut"/></xsl:variable>
		<xsl:variable name="code_surligne"><xsl:value-of select="@surligne"/></xsl:variable>
		<xsl:value-of select="php:function('zcode::geshi_code', $code_content, $code_type, $code_debut, $code_surligne)" disable-output-escaping="yes"/>
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES TABLEAUX -->
<xsl:template match="tableau">
	<table class="tab_user">
			<xsl:apply-templates />
	</table>
</xsl:template>

<xsl:template match="legende"><caption><xsl:value-of select="." /></caption></xsl:template>

<xsl:template match="ligne"><tr><xsl:apply-templates /></tr></xsl:template>

<xsl:template match="cellule">
	<xsl:element name="td">
		<xsl:if test="@fusion_lig"><xsl:attribute name="rowspan"><xsl:value-of select="@fusion_lig" /></xsl:attribute></xsl:if>
		<xsl:if test="@fusion_col"><xsl:attribute name="colspan"><xsl:value-of select="@fusion_col" /></xsl:attribute></xsl:if>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<xsl:template match="entete"><th><xsl:apply-templates /></th></xsl:template>

<!-- TRAITEMENT DES TITRES -->
<xsl:template match="titre1"><h3><xsl:apply-templates /></h3></xsl:template>
<xsl:template match="titre2"><h4><xsl:apply-templates /></h4></xsl:template>


<!-- TRAITEMENT DES LIENS -->
<xsl:template match="lien">
	<xsl:element name="a">
		<xsl:attribute name="href">
			<xsl:choose>
					<xsl:when test="@url">
						<xsl:variable name="lien_langue">
							<xsl:choose>
								<xsl:when test="@langue='fr' or @langue='en'"><xsl:value-of select="@langue" /></xsl:when>
								<xsl:otherwise>fr</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<xsl:choose>
							<xsl:when test="@type = 'wikipedia'">http://<xsl:value-of select="$lien_langue" />.wikipedia.org/wiki/<xsl:value-of select="@url"/></xsl:when>
							<xsl:when test="@type = 'google'">http://www.google.com/search?hl=<xsl:value-of select="$lien_langue" />&amp;q=<xsl:value-of select="@url"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="@url" /></xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise><xsl:value-of select="." /></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES MAILS -->
<xsl:template match="email">
	<xsl:element name="a">
		<xsl:attribute name="href">
		mailto:<xsl:choose>
				<xsl:when test="@nom"><xsl:value-of select="@nom" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="." /></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>


<!-- TRAITEMENT DES CITATIONS -->
<xsl:template match="citation">
	<xsl:variable name="citation_titre">
		<xsl:choose>
			<xsl:when test="@nom">
				<span class="citation">citation : <xsl:value-of select="@nom" /></span>
			</xsl:when>
			<xsl:otherwise>
				<span class="citation">citation</span>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:choose>
	<xsl:when test="@lien"><xsl:element name="a"><xsl:attribute name="href"><xsl:value-of select="@lien" /></xsl:attribute><xsl:copy-of select="$citation_titre" /></xsl:element></xsl:when>
	<xsl:otherwise><xsl:copy-of select="$citation_titre" /></xsl:otherwise>
	</xsl:choose>
	<div class="citation2"><xsl:apply-templates /></div>
</xsl:template>

<!-- TRAITEMENT DES COULEURS sous la forme nom_couleur -->
<xsl:template match="couleur">
	<xsl:element name="span">
		<xsl:attribute name="class"><xsl:value-of select="@nom" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES POLICES DE CARACTERES -->
<xsl:template match="police">
	<xsl:element name="span">
		<xsl:attribute name="class"><xsl:value-of select="@nom" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES IMAGES -->
<xsl:template match="image">
	<xsl:element name="img">
				<xsl:attribute name="src"><xsl:value-of select="." /></xsl:attribute>
				<xsl:choose>
					<xsl:when test="@legende"><xsl:attribute name="alt"><xsl:value-of select="@legende" /></xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="alt">Image utilisateur</xsl:attribute></xsl:otherwise>
				</xsl:choose>
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES ZONES DE SECRET -->
<!-- penser à définir les fonction JS nécessaires -->
<xsl:template match="secret">
<span class="spoiler_hidden">Secret <a href="#">(cliquez pour afficher)</a></span>
<div class="spoiler2_hidden">
	<div class="spoiler3_hidden">
		<xsl:apply-templates />
	</div>
</div>
</xsl:template>

<!-- TRAITEMENT DES INDICES ET EXPOSANTS -->
<xsl:template match="indice"><sub><xsl:apply-templates /></sub></xsl:template>
<xsl:template match="exposant"><sup><xsl:apply-templates /></sup></xsl:template>

<!-- TRAITEMENT DES ACRONYMES -->
<xsl:template match="acronyme">
	<xsl:element name="acronym">
		<xsl:attribute name="title"><xsl:value-of select="@valeur" /></xsl:attribute>
		<xsl:apply-templates />
	</xsl:element>
</xsl:template>

<!-- TRAITEMENT DES SMILIES -->
<xsl:template match="smile">
	<xsl:element name="img">
		<xsl:attribute name="src">zform/img/smilies/<xsl:value-of select="@image" /></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="." /></xsl:attribute>
	</xsl:element>
</xsl:template>

<xsl:template match="//code/smile">
	<xsl:value-of select="@image" />
</xsl:template>

<!-- ON AUTORISE GLOBALEMENT LES <br /> -->
<xsl:template match="br">
<br/>
</xsl:template>

<!-- ÉLEMENTS DANS LEQUELS ON VEUT SUPPRIMER LES <br /> : pas de br dans: code, liste, tableau et tr -->
<xsl:template match="//code/br|//liste/br|//tableau/br|//ligne/br">
</xsl:template>

<xsl:template match="script">
	&lt;<xsl:value-of select="." />&gt;
</xsl:template>

</xsl:stylesheet>